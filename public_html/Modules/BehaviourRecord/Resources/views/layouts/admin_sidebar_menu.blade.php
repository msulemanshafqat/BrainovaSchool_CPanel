<li class="sidebar-menu-item {{ set_menu(['incidents/*']) }}">
    <a class="parent-item-content has-arrow">
        <i class="las la-swatchbook"></i>
        <span class="on-half-expanded">{{ ___('common.Behaviour record') }}</span>
        @if(env('APP_DEMO'))
                                    <span class="badge badge-danger text-white">{{ ___('addon.Pro') }}</span>
                                @endif
    </a>
    <ul class="child-menu-list">
        @if (hasPermission('certificate_read'))
            <li
                class="sidebar-menu-item {{ set_menu(['incidents', 'incidents/create', 'incidents/edit*']) }}">
                <a href="{{ route('incidents.index') }}">{{ ___('common.Incidents') }}</a>
            </li>
        @endif

        @if (hasPermission('certificate_generate_read'))
                <li class="sidebar-menu-item {{ set_menu(['incidents/record']) }}">
                <a
                    href="{{ route('incidents.record.index') }}">{{ ___('common.Record') }}</a>
            </li>
        @endif
        @if (hasPermission('certificate_generate_read'))
            <li class="sidebar-menu-item {{ set_menu(['incidents/appeal']) }}">
                <a
                    href="{{ route('incidents.appeal.index') }}">{{ ___('common.Appeals') }}</a>
            </li>
        @endif

        @if (hasPermission('certificate_generate_read'))
            <li class="sidebar-menu-item {{ set_menu(['incidents/reports*']) }}">
                <a href="{{ route('incidents.reports.index') }}">{{ ___('common.Report') }}</a>
            </li>
        @endif
    </ul>
</li>
