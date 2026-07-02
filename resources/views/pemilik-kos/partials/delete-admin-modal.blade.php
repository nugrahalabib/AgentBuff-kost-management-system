{{-- Delete Admin Modal --}}
<template x-teleport="body">
    <div x-show="showDeleteModal" x-cloak class="fixed inset-0 z-[9999] overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black/50" @click="showDeleteModal = false; deleteUserId = null"></div>

            <div class="relative bg-white rounded-2xl shadow-xl max-w-md w-full p-6 z-10">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold text-gray-800">Hapus Akun Admin</h3>
                    <button @click="showDeleteModal = false; deleteUserId = null"
                        class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="mb-6">
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-red-600 mt-0.5 mr-3" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.664 1.732-2.992l-6.928-12A2 2 0 0015 2.992 2 2 0 0012 2a2 2 0 00-1.732 1.008l-6.928 12C2.608 19.336 3.57 21 5.11 21z">
                                </path>
                            </svg>
                            <div>
                                <p class="text-sm font-bold text-red-800">Tindakan Irreversible!</p>
                                <p class="text-sm text-red-700 mt-1">
                                    Akun admin akan dihapus secara permanen. Data aktivitas akan tetap tersimpan untuk
                                    audit trail, namun user tidak dapat login lagi.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-6">
                    <p class="text-sm text-gray-600">
                        Apakah Anda yakin ingin menghapus akun admin ini? Ketik <span
                            class="font-bold text-red-600">HAPUS</span> untuk konfirmasi:
                    </p>
                    <input type="text" x-model="deleteConfirmation" placeholder="Ketik HAPUS"
                        class="mt-2 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500">
                </div>

                <div class="flex gap-3">
                    <button type="button" @click="showDeleteModal = false; deleteUserId = null"
                        class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                        Batal
                    </button>
                    <button type="button" @click="performDelete()"
                        class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 font-bold">
                        Hapus Permanen
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>