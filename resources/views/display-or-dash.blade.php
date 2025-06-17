{{-- File: resources/views/infolists/entries/display-or-dash.blade.php --}}

{{--
  Struktur ini meniru komponen TextEntry bawaan Filament.
  Anda bisa menyalin-tempel kode ini sepenuhnya.
--}}
<div class="fi-in-entry-wrp">

    {{-- BAGIAN UNTUK MENAMPILKAN LABEL --}}
    <dt class="fi-in-entry-wrp-label text-sm font-medium leading-6 text-gray-950 dark:text-white">
        {{ $getLabel() }}
    </dt>

    {{-- BAGIAN UNTUK MENAMPILKAN NILAI (VALUE) --}}
    <dd class="text-sm text-white-500 dark:text-dark-400">
        {{ $getState() ?? '-' }}
    </dd>

</div>
