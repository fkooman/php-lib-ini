%global composer_vendor  fkooman
%global composer_project ini

%global github_owner     fkooman
%global github_name      php-lib-ini

Name:       php-%{composer_vendor}-%{composer_project}
Version:    1.0.0
Release:    3%{?dist}
Summary:    Handle INI configuration files

Group:      System Environment/Libraries
License:    ASL 2.0
URL:        https://github.com/%{github_owner}/%{github_name}
Source0:    https://github.com/%{github_owner}/%{github_name}/archive/%{version}.tar.gz
Source1:    %{name}-autoload.php

BuildArch:  noarch

Provides:   php-composer(%{composer_vendor}/%{composer_project}) = %{version}

Requires:   php(language) >= 5.3.3
Requires:   php-spl
Requires:   php-standard
Requires:   php-composer(symfony/class-loader)

BuildRequires:  php-composer(symfony/class-loader)
BuildRequires:  %{_bindir}/phpunit
BuildRequires:  %{_bindir}/phpab

%description
Simple library for reading INI-style configuration files.

%prep
%setup -qn %{github_name}-%{version}
cp %{SOURCE1} src/%{composer_vendor}/Ini/autoload.php

%build

%install
mkdir -p ${RPM_BUILD_ROOT}%{_datadir}/php
cp -pr src/* ${RPM_BUILD_ROOT}%{_datadir}/php

%check
%{_bindir}/phpab --output tests/bootstrap.php tests
echo 'require "%{buildroot}%{_datadir}/php/%{composer_vendor}/Ini/autoload.php";' >> tests/bootstrap.php
%{_bindir}/phpunit \
    --bootstrap tests/bootstrap.php

%files
%defattr(-,root,root,-)
%dir %{_datadir}/php/%{composer_vendor}/Ini
%{_datadir}/php/%{composer_vendor}/Ini/*
%doc README.md CHANGES.md composer.json
%license COPYING

%changelog
* Wed Sep 02 2015 François Kooman <fkooman@tuxed.net> - 1.0.0-3
- require phpab

* Wed Sep 02 2015 François Kooman <fkooman@tuxed.net> - 1.0.0-2
- run tests on build
- include autoload script

* Mon Jul 13 2015 François Kooman <fkooman@tuxed.net> - 1.0.0-1
- update to 1.0.0

* Thu Oct 23 2014 François Kooman <fkooman@tuxed.net> - 0.2.0-1
- update to 0.2.0

* Wed Oct 22 2014 François Kooman <fkooman@tuxed.net> - 0.1.0-1
- initial package
