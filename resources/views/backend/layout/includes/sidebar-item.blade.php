@php
    $children = $menu['children'] ?? [];
    $hasChildren = !empty($children);
    $isActive = !empty($menu['is_active']);
    $isExactActive = !empty($menu['is_exact_active']);
    $isOpen = !empty($menu['is_open']);
@endphp

<li
    class="sidebar-tree__item sidebar-tree__item--level-{{ $level }} {{ $hasChildren ? 'has-children' : 'is-leaf' }} {{ $isOpen ? 'is-open' : '' }} {{ $isActive ? 'is-active' : '' }} {{ $isExactActive ? 'is-exact-active' : '' }}"
    data-sidebar-item
    data-sidebar-level="{{ $level }}"
    style="--sidebar-level: {{ $level }}"
    role="treeitem"
    aria-expanded="{{ $hasChildren ? ($isOpen ? 'true' : 'false') : 'false' }}"
>
    @if($hasChildren)
        <button type="button" class="sidebar-tree__button" data-sidebar-toggle>
            <span class="sidebar-tree__icon"><i class="{{ $menu['icon'] }}"></i></span>
            <span class="sidebar-tree__text">{{ $menu['name'] }}</span>
            <span class="sidebar-tree__chevron"><i class="mdi mdi-chevron-down"></i></span>
        </button>
        <ul class="sidebar-tree__children" role="group">
            @foreach($children as $child)
                @include('backend.layout.includes.sidebar-item', [
                    'menu' => $child,
                    'level' => $level + 1,
                ])
            @endforeach
        </ul>
    @else
        <a class="sidebar-tree__link" href="{{ $menu['url'] }}">
            <span class="sidebar-tree__icon"><i class="{{ $menu['icon'] }}"></i></span>
            <span class="sidebar-tree__text">{{ $menu['name'] }}</span>
        </a>
    @endif
</li>
