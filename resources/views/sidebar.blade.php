<li class="nav-item{{ is_current_path('admin-phonebook') ? ' active' : '' }}">
    <a href="{{ route('admin-phonebook') }}" class="nav-link">
        <i class="material-icons">dashboard</i>
        <p>PhoneBook</p>
    </a>
</li>
<li class="nav-item{{ is_current_path('admin-phonebook-contacts') ? ' active' : '' }}">
    <a href="{{ route('admin-phonebook-contacts') }}" class="nav-link">
        <i class="material-icons">contacts</i>
        <p>Contacts</p>
    </a>
</li>
