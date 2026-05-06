<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Repositories\UserRepository;
use App\Http\Controllers\AppBaseController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Flash;

/**
 * Manajemen User & Admin.
 *
 * Hanya bisa diakses oleh admin (di-guard via middleware 'admin' di route).
 * Termasuk fitur tambah, edit, hapus, dan promote/demote role.
 *
 * Guards keamanan:
 *   - Tidak bisa hapus diri sendiri
 *   - Tidak bisa demote diri sendiri (avoid self lock-out)
 *   - Minimal 1 admin harus tersisa di sistem (avoid no-admin lock-out)
 */
class UserController extends AppBaseController
{
    /** @var UserRepository */
    private $userRepository;

    public function __construct(UserRepository $userRepo)
    {
        $this->userRepository = $userRepo;
    }

    /**
     * Display a listing of the User.
     */
    public function index(Request $request)
    {
        $users = User::orderBy('role', 'asc') // admin di atas
            ->orderBy('name', 'asc')
            ->get();

        $totalAdmin = $users->where('role', 'admin')->count();
        $totalUser  = $users->where('role', 'user')->count();

        return view('dashboard.users.index')
            ->with('users', $users)
            ->with('totalAdmin', $totalAdmin)
            ->with('totalUser', $totalUser);
    }

    /**
     * Show the form for creating a new User.
     */
    public function create()
    {
        return view('dashboard.users.create');
    }

    /**
     * Store a newly created User in storage.
     */
    public function store(CreateUserRequest $request)
    {
        $input = $request->validated();
        $input['password'] = Hash::make($input['password']);

        $user = $this->userRepository->create($input);

        Flash::success('User "' . $user->name . '" berhasil ditambahkan sebagai ' . ($user->isAdmin() ? 'Admin' : 'User') . '.');

        return redirect(route('users.index'));
    }

    /**
     * Show the form for editing the specified User.
     */
    public function edit($id)
    {
        $user = $this->userRepository->find($id);

        if (empty($user)) {
            Flash::error('User tidak ditemukan.');
            return redirect(route('users.index'));
        }

        return view('dashboard.users.edit')->with('user', $user);
    }

    /**
     * Update the specified User in storage.
     */
    public function update($id, UpdateUserRequest $request)
    {
        $user = $this->userRepository->find($id);

        if (empty($user)) {
            Flash::error('User tidak ditemukan.');
            return redirect(route('users.index'));
        }

        $input = $request->validated();

        // Guard: tidak boleh demote diri sendiri (avoid self lock-out)
        if ($user->id === Auth::id() && $input['role'] !== 'admin') {
            Flash::error('Anda tidak bisa mengubah role diri sendiri menjadi non-admin.');
            return redirect(route('users.edit', $user->id));
        }

        // Guard: minimal 1 admin harus tersisa
        if ($user->isAdmin() && $input['role'] !== 'admin') {
            $adminCount = User::where('role', 'admin')->count();
            if ($adminCount <= 1) {
                Flash::error('Tidak bisa demote admin terakhir. Sistem harus memiliki minimal 1 admin.');
                return redirect(route('users.edit', $user->id));
            }
        }

        // Password: hanya update jika diisi (kosongkan = keep existing)
        if (!empty($input['password'])) {
            $input['password'] = Hash::make($input['password']);
        } else {
            unset($input['password']);
        }

        $this->userRepository->update($input, $id);

        Flash::success('User "' . $user->name . '" berhasil diperbarui.');

        return redirect(route('users.index'));
    }

    /**
     * Remove the specified User from storage.
     */
    public function destroy($id)
    {
        $user = $this->userRepository->find($id);

        if (empty($user)) {
            Flash::error('User tidak ditemukan.');
            return redirect(route('users.index'));
        }

        // Guard: tidak boleh hapus diri sendiri
        if ($user->id === Auth::id()) {
            Flash::error('Anda tidak bisa menghapus akun sendiri.');
            return redirect(route('users.index'));
        }

        // Guard: minimal 1 admin harus tersisa
        if ($user->isAdmin()) {
            $adminCount = User::where('role', 'admin')->count();
            if ($adminCount <= 1) {
                Flash::error('Tidak bisa menghapus admin terakhir. Sistem harus memiliki minimal 1 admin.');
                return redirect(route('users.index'));
            }
        }

        $userName = $user->name;
        $this->userRepository->delete($id);

        Flash::success('User "' . $userName . '" berhasil dihapus.');

        return redirect(route('users.index'));
    }
}
