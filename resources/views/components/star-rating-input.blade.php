@props(['name' => 'rating'])

@php($uid = uniqid('star_'))

<fieldset class="star-rating-input inline-flex flex-row-reverse gap-1">
    <legend class="sr-only">Rating</legend>
    @for ($i = 5; $i >= 1; $i--)
        <input type="radio" id="{{ $uid }}-{{ $i }}" name="{{ $name }}" value="{{ $i }}"
            class="sr-only" required />
        <label for="{{ $uid }}-{{ $i }}"
            class="cursor-pointer rounded-md text-3xl leading-none text-brand/15 transition-colors duration-150">
            <span aria-hidden="true">★</span>
            <span class="sr-only">{{ $i }} {{ str('star')->plural($i) }}</span>
        </label>
    @endfor
</fieldset>

<style>
    .star-rating-input input:checked ~ label,
    .star-rating-input label:hover,
    .star-rating-input label:hover ~ label {
        color: var(--color-amber);
    }

    .star-rating-input input:focus-visible + label {
        outline: 2px solid var(--color-brand);
        outline-offset: 2px;
    }
</style>
