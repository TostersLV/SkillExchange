@props(['name' => 'rating'])

@php($uid = uniqid('star_'))

<div class="star-rating-input inline-flex flex-row-reverse gap-1">
    @for ($i = 5; $i >= 1; $i--)
        <input type="radio" id="{{ $uid }}-{{ $i }}" name="{{ $name }}" value="{{ $i }}"
            class="sr-only" required />
        <label for="{{ $uid }}-{{ $i }}"
            class="cursor-pointer text-4xl leading-none text-zinc-300 dark:text-zinc-600">★</label>
    @endfor
</div>

<style>
    .star-rating-input input:checked ~ label,
    .star-rating-input label:hover,
    .star-rating-input label:hover ~ label {
        color: var(--color-amber-400);
    }
</style>
