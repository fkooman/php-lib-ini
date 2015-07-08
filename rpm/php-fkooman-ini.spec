%global composer_vendor  fkooman
%global composer_project ini

%global github_owner     fkooman
%global github_name      php-lib-ini

Name:       php-%{composer_vendor}-%{composer_project}
Version:    0.2.0
Release:    1%{?dist}
Summary:    Handle INI configuration files

Group:      System Environment/Libraries
License:    ASL 2.0
URL:        https://github.com/%{github_owner}/%{github_name}
Source0:    https://github.com/%{github_owner}/%{github_name}/archive/%{version}.tar.gz
BuildArch:  noarch

Provides:   php-composer(%{composer_vendor}/%{composer_project}) = %{version}

Requires:   php(language) >= 5.3.3
Requires:   php-spl

%description
Simple library for reading INI-style configuration files.

%prep
%setup -qn %{github_name}-%{version}

%build

%install
mkdir -p ${RPM_BUILD_ROOT}%{_datadir}/php
cp -pr src/* ${RPM_BUILD_ROOT}%{_datadir}/php

%files
%defattr(-,root,root,-)
%dir %{_datadir}/php/%{composer_vendor}/Ini
%{_datadir}/php/%{composer_vendor}/Ini/*
%doc README.md CHANGES.md composer.json
%license COPYING

%changelog
* Thu Oct 23 2014 François Kooman <fkooman@tuxed.net> - 0.2.0-1
- update to 0.2.0

* Wed Oct 22 2014 François Kooman <fkooman@tuxed.net> - 0.1.0-1
- initial package
