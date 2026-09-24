{{-- Palette-styled select. All attributes pass straight through to flux:select; options go in the slot. --}}
<flux:select :attributes="$attributes">
    {{ $slot }}
</flux:select>
