@php
    $link = '';
@endphp

<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        @foreach (request()->segments() as $seg)
            @if ($loop->last)
                <li class="breadcrumb-item "> {{ ucwords(str_replace('-', ' ', $seg)) }}</li>
            @elseif ($loop->first)
                <li class="breadcrumb-item active">
                    <a href="{{route('admin.home')}}">{{ __('lang.Home') }}</a>
                </li>
            @else
            @php $link .= "/" . $seg; @endphp
                <li class="breadcrumb-item {{ $loop->first ? 'active' : '' }}">
                    <a href="{{ route('admin.'.$seg) }}">
                        {{ __('lang.'.ucwords(str_replace('-', ' ', $seg))) }}
                    </a>
                </li>
            @endif
        @endforeach
    </ol>
</nav>
