@extends('layouts.pemilik-kos')

@section('header')
    <div x-data class="flex flex-col md:flex-row justify-between items-center gap-4">
        <div>
            <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                {{ __('Manajemen Akses') }}
            </h2>
            <p class="text-sm text-gray-500">Kontrol penuh akun staf dan rekam jejak aktivitas sistem (Audit Trail).</p>
        </div>
        <button @click="$dispatch('open-add-admin-modal')"
            class="bg-emerald-800 hover:bg-emerald-900 text-white text-sm font-bold py-3 px-6 rounded-xl shadow-lg transition flex items-center transform hover:scale-105">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            TAMBAH ADMIN BARU
        </button>
    </div>
@endsection

@section('content')
    <div class="space-y-8" x-data="adminControl()" @open-add-admin-modal.window="openAddModal = true">

        <div>
            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                    </path>
                </svg>
                Tim Manajemen Aktif ({{ $admins->count() }})
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($admins as $admin)
                    <div
                        class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden group hover:border-emerald-300 transition {{ $admin->status === 'inactive' ? 'bg-gray-50 opacity-75' : '' }}">
                        <div class="p-6 flex items-start justify-between">
                            <div class="flex gap-4">
                                <img class="h-14 w-14 rounded-full object-cover {{ $admin->status === 'inactive' ? 'grayscale' : 'border-2 border-emerald-100' }}"
                                    src="https://ui-avatars.com/api/?name={{ urlencode($admin->name) }}&background={{ $admin->status === 'active' ? '065f46' : '9CA3AF' }}&color=fff"
                                    alt="{{ $admin->name }}">
                                <div>
                                    <h4
                                        class="text-lg font-bold {{ $admin->status === 'inactive' ? 'text-gray-500' : 'text-gray-800' }}">
                                        {{ $admin->name }} {{ $admin->status === 'inactive' ? '(Resign)' : '' }}
                                    </h4>
                                    <span
                                        class="px-2 py-0.5 rounded border text-xs font-bold
                                            {{ $admin->status === 'active' && $admin->position === 'Admin Keuangan' ? 'bg-emerald-100 text-emerald-800 border-emerald-200' : '' }}
                                            {{ $admin->status === 'active' && $admin->position !== 'Admin Keuangan' ? 'bg-gray-100 text-gray-600 border-gray-200' : '' }}
                                            {{ $admin->status === 'inactive' ? 'bg-gray-200 text-gray-500 border-gray-300' : '' }}">
                                        {{ $admin->status === 'inactive' ? 'Non-Aktif' : $admin->position }}
                                    </span>
                                </div>
                            </div>
                            <div class="relative inline-block w-10 mr-2 align-middle select-none">
                                <input type="checkbox" @click="toggleStatus({{ $admin->id }})" {{ $admin->status === 'active' ? 'checked' : '' }}
                                    class="toggle-checkbox absolute block w-5 h-5 rounded-full bg-white border-4 appearance-none cursor-pointer" />
                                <label
                                    class="toggle-label block overflow-hidden h-5 rounded-full cursor-pointer {{ $admin->status === 'active' ? 'bg-emerald-400' : 'bg-gray-300' }}"></label>
                            </div>
                        </div>
                        <div class="px-6 pb-4">
                            <div
                                class="flex items-center text-xs {{ $admin->status === 'inactive' ? 'bg-white border border-gray-100' : 'bg-gray-50' }} p-2 rounded-lg text-gray-500">
                                @if($admin->status === 'active')
                                    <svg class="w-4 h-4 mr-2 {{ $admin->last_login_at && $admin->last_login_at->diffInMinutes() < 30 ? 'text-emerald-500' : 'text-gray-400' }}"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Terakhir login: <span
                                        class="font-bold text-gray-700 ml-1">{{ $admin->last_login_at ? $admin->last_login_at->diffForHumans() : 'Belum pernah' }}</span>
                                @else
                                    <svg class="w-4 h-4 mr-2 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                                        </path>
                                    </svg>
                                    Akses ditutup: <span
                                        class="font-bold text-gray-700 ml-1">{{ $admin->updated_at->format('d M Y') }}</span>
                                @endif
                            </div>
                        </div>
                        <div
                            class="border-t {{ $admin->status === 'inactive' ? 'border-gray-200 bg-gray-100' : 'border-gray-100 bg-gray-50' }} p-4 flex gap-2">
                            @if($admin->status === 'active')
                                <button @click='openEditModal(@json($admin))'
                                    class="flex-1 bg-white border border-gray-300 text-gray-600 hover:text-emerald-700 hover:border-emerald-500 text-xs font-bold py-2 rounded-lg transition">Edit
                                    Profil</button>
                                <button @click="openResetPasswordModal({{ $admin->id }})"
                                    class="flex-1 bg-white border border-gray-300 text-gray-600 hover:text-red-700 hover:border-red-500 text-xs font-bold py-2 rounded-lg transition">Reset
                                    Password</button>
                            @else
                                <button @click="openDeleteModal({{ $admin->id }})"
                                    class="w-full bg-white border border-gray-300 text-red-600 hover:bg-red-50 text-xs font-bold py-2 rounded-lg transition">Hapus
                                    Akun Permanen</button>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="col-span-3 text-center py-12 text-gray-500">
                        Belum ada admin staff. Klik "TAMBAH ADMIN BARU" untuk menambahkan.
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Audit Trail Table --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div
                class="p-6 border-b border-gray-100 flex flex-col md:flex-row justify-between items-center gap-4 bg-gray-50/50">
                <div>
                    <h3 class="text-lg font-bold text-gray-800">Jejak Audit Sistem</h3>
                    <p class="text-sm text-gray-500">
                        Menampilkan <span id="auditVisibleCount"
                            class="font-semibold text-gray-700">{{ $activityLogs->count() }}</span> aktivitas
                        dari <span
                            class="font-semibold text-gray-700">{{ \Carbon\Carbon::parse($dateFrom)->format('d M Y') }}</span>
                        s/d <span
                            class="font-semibold text-gray-700">{{ \Carbon\Carbon::parse($dateTo)->format('d M Y') }}</span>.
                    </p>
                </div>
                <div class="flex flex-wrap gap-2 items-center">
                    <select id="actorFilter" onchange="applyAuditFilter()"
                        class="border border-gray-300 rounded-lg text-sm px-3 py-2 text-gray-600 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                        <option value="">Semua Aktor</option>
                        @php
                            $uniqueActors = $activityLogs->pluck('admin.name')
                                ->merge($admins->pluck('name'))
                                ->unique()->sort();
                        @endphp
                        @foreach($uniqueActors as $actor)
                            <option value="{{ $actor }}">{{ $actor }}</option>
                        @endforeach
                    </select>
                    <input type="date" x-model="dateFrom"
                        class="border border-gray-300 rounded-lg text-sm px-3 py-2 text-gray-600 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    <span class="text-gray-400 text-sm">s/d</span>
                    <input type="date" x-model="dateTo"
                        class="border border-gray-300 rounded-lg text-sm px-3 py-2 text-gray-600 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    <button @click="filterLogs()"
                        class="bg-emerald-700 hover:bg-emerald-800 text-white px-4 py-2 rounded-lg text-sm font-bold transition shadow-sm flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z" />
                        </svg>
                        Cari
                    </button>
                    <a :href="`{{ route('owner.admin.export-audit') }}?date_from=${dateFrom}&date_to=${dateTo}`"
                        class="bg-white border border-gray-300 text-gray-600 hover:text-emerald-700 px-4 py-2 rounded-lg text-sm font-bold transition shadow-sm">
                        Unduh Log (PDF)
                    </a>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm whitespace-nowrap">
                    <thead class="bg-gray-100 text-gray-500 font-bold uppercase text-xs tracking-wider">
                        <tr>
                            <th class="px-6 py-4">Waktu</th>
                            <th class="px-6 py-4">Aktor (User)</th>
                            <th class="px-6 py-4">Aktivitas</th>
                            <th class="px-6 py-4">Detail Perubahan</th>
                            <th class="px-6 py-4 text-right">IP Address</th>
                        </tr>
                    </thead>
                    <tbody id="auditTableBody" class="divide-y divide-gray-100">
                        @forelse($activityLogs as $log)
                            <tr class="audit-row hover:bg-gray-50 transition 
                                    {{ in_array($log->activity_type, ['update_status', 'update']) ? 'border-l-4 border-l-yellow-400 bg-yellow-50/20' : '' }}
                                    {{ in_array($log->activity_type, ['hapus_data', 'delete']) ? 'border-l-4 border-l-red-500 bg-red-50/20' : '' }}"
                                data-actor="{{ $log->admin->name }}">
                                <td class="px-6 py-4 text-gray-500 font-mono text-xs">
                                    {{ $log->created_at->format('d M Y, H:i:s') }}</td>
                                <td class="px-6 py-4 flex items-center gap-3">
                                    @php
                                        $bgColor = match ($log->admin->position ?? '') {
                                            'Admin Keuangan' => 'bg-emerald-100 text-emerald-700',
                                            'Owner' => 'bg-gray-800 text-white',
                                            default => 'bg-gray-600 text-white'
                                        };
                                        $initials = collect(explode(' ', $log->admin->name))->map(fn($word) => strtoupper(substr($word, 0, 1)))->take(2)->implode('');
                                    @endphp
                                    <div
                                        class="w-6 h-6 rounded-full {{ $bgColor }} flex items-center justify-center text-xs font-bold">
                                        {{ $initials }}</div>
                                    <span class="font-bold text-gray-800">{{ $log->admin->name }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $badgeColor = match ($log->activity_type) {
                                            'input_transaksi', 'input' => 'bg-blue-100 text-blue-700 border-blue-200',
                                            'login' => 'bg-gray-100 text-gray-700 border-gray-200',
                                            'update_status', 'update' => 'bg-yellow-100 text-yellow-700 border-yellow-200',
                                            'hapus_data', 'delete' => 'bg-red-100 text-red-700 border-red-200',
                                            default => 'bg-gray-100 text-gray-700 border-gray-200'
                                        };
                                    @endphp
                                    <span
                                        class="px-2 py-1 rounded text-xs font-bold border {{ $badgeColor }}">{{ $log->activity_label }}</span>
                                </td>
                                <td class="px-6 py-4 text-gray-600">{{ $log->notes }}</td>
                                <td class="px-6 py-4 text-right text-gray-400 font-mono text-xs">{{ $log->ip_address }}</td>
                            </tr>
                        @empty
                            <tr id="auditEmptyRow">
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                    Belum ada aktivitas yang tercatat.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div id="auditPagination"
                class="p-4 border-t border-gray-100 flex flex-col sm:flex-row justify-between items-center gap-3 bg-gray-50/50">
                <p class="text-sm text-gray-500">Halaman <span id="auditCurrentPage" class="font-semibold">1</span> dari
                    <span id="auditTotalPages" class="font-semibold">1</span></p>
                <div id="auditPaginationButtons" class="flex gap-1"></div>
            </div>
        </div>

        @include('pemilik-kos.partials.add-admin-modal')
        @include('pemilik-kos.partials.edit-admin-modal')
        @include('pemilik-kos.partials.reset-password-modal')
        @include('pemilik-kos.partials.delete-admin-modal')

    </div>

    <style>
        .toggle-checkbox:checked {
            right: 0;
            border-color: #10B981;
        }

        .toggle-checkbox:checked+.toggle-label {
            background-color: #10B981;
        }

        .toggle-checkbox {
            right: 0;
            transition: all 0.3s;
        }
    </style>

    <script>
        function adminControl() {
            return {
                openAddModal: false,
                showEditModal: false,
                editingAdmin: null,
                showResetPasswordModal: false,
                resetUserId: null,
                showDeleteModal: false,
                deleteUserId: null,
                dateFrom: '{{ $dateFrom }}',
                dateTo: '{{ $dateTo }}',

                addForm: {
                    name: '',
                    email: '',
                    position: '',
                    password: '',
                    password_confirmation: ''
                },

                // Password Reset State
                tempPassword: null,
                showResetSuccess: false,
                resetNewPassword: '',
                resetPasswordConfirmation: '',
                showNewPassword: false,
                showConfirmPassword: false,

                // Delete State
                deleteUserId: null,
                deleteConfirmation: '',

                async submitAddAdmin() {
                    try {
                        const response = await fetch("{{ route('owner.admin.store') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify(this.addForm)
                        });

                        const data = await response.json();

                        if (data.success) {
                            this.openAddModal = false;
                            sessionStorage.setItem('toastMessage', JSON.stringify({ icon: 'success', title: 'Admin berhasil ditambahkan!' }));
                            window.location.reload();
                        } else if (data.errors) {
                            this.openAddModal = false;
                            const firstError = Object.values(data.errors)[0][0];
                            Toast.fire({ icon: 'error', title: firstError });
                        } else {
                            this.openAddModal = false;
                            Toast.fire({ icon: 'error', title: data.message || 'Terjadi kesalahan' });
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        this.openAddModal = false;
                        Toast.fire({ icon: 'error', title: 'Terjadi kesalahan' });
                    }
                },

                openEditModal(admin) {
                    this.editingAdmin = { ...admin };
                    this.showEditModal = true;
                },

                async submitEditForm() {
                    if (!this.editingAdmin) return;
                    try {
                        const response = await fetch(`/owner/admin-control/${this.editingAdmin.id}`, {
                            method: 'PATCH',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                name: this.editingAdmin.name,
                                email: this.editingAdmin.email,
                                position: this.editingAdmin.position
                            })
                        });
                        const data = await response.json();
                        if (data.success) {
                            this.showEditModal = false;
                            this.editingAdmin = null;
                            sessionStorage.setItem('toastMessage', JSON.stringify({ icon: 'success', title: 'Profil admin berhasil diupdate!' }));
                            window.location.reload();
                        } else if (data.errors) {
                            this.showEditModal = false;
                            this.editingAdmin = null;
                            const firstError = Object.values(data.errors)[0][0];
                            Toast.fire({ icon: 'error', title: firstError });
                        } else {
                            this.showEditModal = false;
                            this.editingAdmin = null;
                            Toast.fire({ icon: 'error', title: data.message || 'Terjadi kesalahan' });
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        this.showEditModal = false;
                        this.editingAdmin = null;
                        Toast.fire({ icon: 'error', title: 'Terjadi kesalahan' });
                    }
                },

                openResetPasswordModal(userId) {
                    this.resetUserId = userId;
                    this.tempPassword = null;
                    this.showResetSuccess = false;
                    this.resetNewPassword = '';
                    this.resetPasswordConfirmation = '';
                    this.showNewPassword = false;
                    this.showConfirmPassword = false;
                    this.showResetPasswordModal = true;
                },

                async confirmResetPassword() {
                    if (!this.resetUserId) return;
                    if (this.resetNewPassword.length < 8) return;
                    if (this.resetNewPassword !== this.resetPasswordConfirmation) return;

                    try {
                        const response = await fetch(`/owner/admin-control/${this.resetUserId}/reset-password`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                password: this.resetNewPassword,
                                password_confirmation: this.resetPasswordConfirmation
                            })
                        });

                        const data = await response.json();

                        if (data.success) {
                            this.showResetSuccess = true;
                        } else {
                            Toast.fire({ icon: 'error', title: 'Gagal: ' + data.message });
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        Toast.fire({ icon: 'error', title: 'Terjadi kesalahan saat reset password' });
                    }
                },

                copyPassword() {
                    navigator.clipboard.writeText(this.tempPassword);
                    Toast.fire({ icon: 'success', title: 'Password disalin ke clipboard!' });
                },

                closeResetModal() {
                    this.showResetPasswordModal = false;
                    if (this.showResetSuccess) {
                        window.location.reload(); // Reload only after closing success modal
                    }
                },

                filterLogs() {
                    const url = new URL(window.location.href);
                    url.searchParams.set('date_from', this.dateFrom);
                    url.searchParams.set('date_to', this.dateTo);
                    window.location.href = url.toString();
                },

                openDeleteModal(userId) {
                    this.deleteUserId = userId;
                    this.deleteConfirmation = '';
                    this.showDeleteModal = true;
                },



                async performDelete() {
                    if (!this.deleteUserId) {
                        this.showDeleteModal = false;
                        Toast.fire({ icon: 'error', title: 'ID Admin tidak ditemukan. Silakan refresh halaman.' });
                        return;
                    }

                    if (this.deleteConfirmation !== 'HAPUS') {
                        this.showDeleteModal = false;
                        Toast.fire({ icon: 'warning', title: 'Harap ketik "HAPUS" (huruf besar) untuk melanjutkan.' });
                        return;
                    }

                    try {
                        const response = await fetch(`/owner/admin-control/${this.deleteUserId}`, {
                            method: 'DELETE',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            }
                        });

                        const data = await response.json();

                        if (data.success) {
                            this.showDeleteModal = false;
                            sessionStorage.setItem('toastMessage', JSON.stringify({ icon: 'success', title: 'Admin berhasil dihapus secara permanen.' }));
                            window.location.reload();
                        } else {
                            Toast.fire({ icon: 'error', title: 'Gagal: ' + data.message });
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        Toast.fire({ icon: 'error', title: 'Terjadi kesalahan koneksi saat menghapus.' });
                    }
                },

                async toggleStatus(userId) {
                    try {
                        const response = await fetch(`/owner/admin-control/${userId}/toggle`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            }
                        });

                        const data = await response.json();

                        if (data.success) {
                            window.location.reload();
                        } else {
                            Toast.fire({ icon: 'error', title: 'Gagal: ' + data.message });
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        Toast.fire({ icon: 'error', title: 'Terjadi kesalahan saat mengubah status' });
                    }
                }
            }
        }

        // Audit Trail: Client-side filter + pagination
        const AUDIT_PER_PAGE = 10;
        let auditCurrentPage = 1;

        function getFilteredRows() {
            const actor = document.getElementById('actorFilter').value;
            const rows = document.querySelectorAll('#auditTableBody .audit-row');
            const filtered = [];
            rows.forEach(row => {
                if (!actor || row.dataset.actor === actor) {
                    filtered.push(row);
                }
            });
            return filtered;
        }

        function renderAuditPage() {
            const allRows = document.querySelectorAll('#auditTableBody .audit-row');
            const filtered = getFilteredRows();
            const totalPages = Math.max(1, Math.ceil(filtered.length / AUDIT_PER_PAGE));
            if (auditCurrentPage > totalPages) auditCurrentPage = totalPages;
            const start = (auditCurrentPage - 1) * AUDIT_PER_PAGE;
            const end = start + AUDIT_PER_PAGE;

            allRows.forEach(row => row.style.display = 'none');
            filtered.forEach((row, i) => {
                row.style.display = (i >= start && i < end) ? '' : 'none';
            });

            const emptyRow = document.getElementById('auditEmptyRow');
            if (emptyRow) emptyRow.style.display = filtered.length === 0 ? '' : 'none';

            document.getElementById('auditVisibleCount').textContent = filtered.length;
            document.getElementById('auditCurrentPage').textContent = auditCurrentPage;
            document.getElementById('auditTotalPages').textContent = totalPages;

            const btnContainer = document.getElementById('auditPaginationButtons');
            btnContainer.innerHTML = '';

            const prevBtn = document.createElement('button');
            prevBtn.innerHTML = '&laquo;';
            prevBtn.className = 'px-3 py-1.5 rounded-lg text-sm font-bold transition ' + (auditCurrentPage === 1 ? 'bg-gray-100 text-gray-400 cursor-not-allowed' : 'bg-white border border-gray-300 text-gray-600 hover:bg-emerald-50 hover:text-emerald-700');
            prevBtn.disabled = auditCurrentPage === 1;
            prevBtn.onclick = () => { auditCurrentPage--; renderAuditPage(); };
            btnContainer.appendChild(prevBtn);

            // Smart pagination: show first, last, current ± 1, with ellipsis
            const pages = [];
            for (let i = 1; i <= totalPages; i++) {
                if (i === 1 || i === totalPages || (i >= auditCurrentPage - 1 && i <= auditCurrentPage + 1)) {
                    pages.push(i);
                } else if (pages[pages.length - 1] !== '...') {
                    pages.push('...');
                }
            }
            pages.forEach(p => {
                if (p === '...') {
                    const dots = document.createElement('span');
                    dots.textContent = '...';
                    dots.className = 'px-2 py-1.5 text-sm text-gray-400';
                    btnContainer.appendChild(dots);
                } else {
                    const btn = document.createElement('button');
                    btn.textContent = p;
                    btn.className = 'px-3 py-1.5 rounded-lg text-sm font-bold transition ' + (p === auditCurrentPage ? 'bg-emerald-700 text-white' : 'bg-white border border-gray-300 text-gray-600 hover:bg-emerald-50 hover:text-emerald-700');
                    btn.onclick = () => { auditCurrentPage = p; renderAuditPage(); };
                    btnContainer.appendChild(btn);
                }
            });

            const nextBtn = document.createElement('button');
            nextBtn.innerHTML = '&raquo;';
            nextBtn.className = 'px-3 py-1.5 rounded-lg text-sm font-bold transition ' + (auditCurrentPage === totalPages ? 'bg-gray-100 text-gray-400 cursor-not-allowed' : 'bg-white border border-gray-300 text-gray-600 hover:bg-emerald-50 hover:text-emerald-700');
            nextBtn.disabled = auditCurrentPage === totalPages;
            nextBtn.onclick = () => { auditCurrentPage++; renderAuditPage(); };
            btnContainer.appendChild(nextBtn);

            document.getElementById('auditPagination').style.display = totalPages <= 1 ? 'none' : '';
        }

        function applyAuditFilter() {
            auditCurrentPage = 1;
            renderAuditPage();
        }

        document.addEventListener('DOMContentLoaded', function () {
            // Show toast from sessionStorage after reload
            const toastMsg = sessionStorage.getItem('toastMessage');
            if (toastMsg) {
                sessionStorage.removeItem('toastMessage');
                Toast.fire(JSON.parse(toastMsg));
            }
            renderAuditPage();
        });
    </script>
@endsection