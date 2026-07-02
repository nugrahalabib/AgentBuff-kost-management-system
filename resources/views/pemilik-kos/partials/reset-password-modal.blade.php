{{-- Reset Password Modal --}}
<template x-teleport="body">
    <div x-show="showResetPasswordModal" x-cloak class="fixed inset-0 z-[9999] overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black/50" @click="closeResetModal()"></div>

            <div class="relative bg-white rounded-2xl shadow-xl max-w-md w-full p-6 z-10">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold text-gray-800">Reset Password Admin</h3>
                    <button @click="closeResetModal()"
                        class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Form State -->
                <div x-show="!showResetSuccess">
                    <div class="mb-5">
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-5">
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-yellow-600 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.664 1.732-2.992l-6.928-12A2 2 0 0015 2.992 2 2 0 0012 2a2 2 0 00-1.732 1.008l-6.928 12C2.608 19.336 3.57 21 5.11 21z">
                                    </path>
                                </svg>
                                <div>
                                    <p class="text-sm font-bold text-yellow-800">Peringatan!</p>
                                    <p class="text-sm text-yellow-700 mt-1">
                                        Password admin akan diganti. Masukkan password baru yang ingin ditetapkan.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- New Password -->
                        <div class="mb-4">
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Password Baru</label>
                            <div class="relative">
                                <input
                                    :type="showNewPassword ? 'text' : 'password'"
                                    x-model="resetNewPassword"
                                    placeholder="Masukkan password baru"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm pr-10"
                                />
                                <button type="button" @click="showNewPassword = !showNewPassword"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                    <svg x-show="!showNewPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    <svg x-show="showNewPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                                    </svg>
                                </button>
                            </div>
                            <p x-show="resetNewPassword.length > 0 && resetNewPassword.length < 8"
                                class="text-xs text-red-500 mt-1">Password minimal 8 karakter.</p>
                        </div>

                        <!-- Confirm Password -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Konfirmasi Password</label>
                            <div class="relative">
                                <input
                                    :type="showConfirmPassword ? 'text' : 'password'"
                                    x-model="resetPasswordConfirmation"
                                    placeholder="Ulangi password baru"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm pr-10"
                                />
                                <button type="button" @click="showConfirmPassword = !showConfirmPassword"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                    <svg x-show="!showConfirmPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    <svg x-show="showConfirmPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                                    </svg>
                                </button>
                            </div>
                            <p x-show="resetPasswordConfirmation.length > 0 && resetNewPassword !== resetPasswordConfirmation"
                                class="text-xs text-red-500 mt-1">Password tidak cocok.</p>
                        </div>
                    </div>

                    <div class="flex gap-3">
                        <button type="button" @click="closeResetModal()"
                            class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                            Batal
                        </button>
                        <button type="button" @click="confirmResetPassword()"
                            :disabled="resetNewPassword.length < 8 || resetNewPassword !== resetPasswordConfirmation"
                            class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 font-bold disabled:opacity-50 disabled:cursor-not-allowed">
                            Reset Password
                        </button>
                    </div>
                </div>

                <!-- Success State -->
                <div x-show="showResetSuccess" x-cloak>
                    <div class="text-center mb-6">
                        <div class="w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <h4 class="text-lg font-bold text-gray-800 mb-2">Password Berhasil Direset!</h4>
                        <p class="text-sm text-gray-500">Password admin telah berhasil diubah sesuai yang Anda tetapkan.</p>
                    </div>

                    <button type="button" @click="closeResetModal()"
                        class="w-full px-4 py-2 bg-gray-900 text-white rounded-lg hover:bg-gray-800 font-bold">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
