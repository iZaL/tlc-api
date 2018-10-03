<header id="m_header" class="m-grid__item    m-header " m-minimize-offset="200" m-minimize-mobile-offset="200">
    <div class="m-container m-container--fluid m-container--full-height">
        <div class="m-stack m-stack--ver m-stack--desktop">
            @include('partials._header-brand')
            <div class="m-stack__item m-stack__item--fluid m-header-head" id="m_header_nav">
                <button class="m-aside-header-menu-mobile-close  m-aside-header-menu-mobile-close--skin-dark " id="m_aside_header_menu_mobile_close_btn"><i class="la la-close"></i></button>
{{--                @include('partials._header-hor-menu')--}}
                @include('partials._header-topbar')
            </div>
        </div>
    </div>
</header>