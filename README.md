[![Build Status](https://secure.travis-ci.org/glpi-project/glpi.svg?branch=master)](https://secure.travis-ci.org/glpi-project/glpi)

![GLPi Logo](https://raw.githubusercontent.com/glpi-project/glpi/master/pics/logos/logo-GLPI-250-black.png)

GLPi stands for **Gestionnaire Libre de Parc Informatique** is a Free Asset and IT Management Software package, that provides ITIL Service Desk features, licenses tracking and software auditing.

GLPi features:
* Multilingual support: 45 translations available
* Inventory of computers, peripherals, network printers and any associated components through an interface, with inventory tools such as: OCS Inventory or FusionInventory
* Assignment of equipment by geographical area to users and groups
* Item lifecycle management
* Asset reservation
* Licenses management (ITIL compliant)
* Management of warranty and financial information (purchase order, warranty and extension, damping)
* Management of contracts, contacts, documents related to inventory items
* Incidents, requests, problems and changes management
* Ticket creation through emails, end-user or technician interface
* Ticket lifecycle (assignment, tickets scheduling, solutions, etc.)
* Knowledge base and Frequently-Asked Questions (FAQ)
* Report generator: hardware, network or interventions (support)

Moreover, GLPi supports many [plugins](http://plugins.glpi-project.org) that provide additional features.


## License

It is distributed under the GNU GENERAL PUBLIC LICENSE Version 2 - please consult the file called [COPYING](https://raw.githubusercontent.com/glpi-project/glpi/master/COPYING.txt) for more details.


## Prerequisites

* A web server (Apache, Nginx, IIS, etc.)
* MariaDB (or MySQL < 5.7)
* PHP 5.4 or higher
* Mandatory PHP extensions:
    - json
    - mbstring
    - mysqli
    - session

* Recommended PHP extensions (to enable optional features)
    - curl (CAS authentication)
    - domxml (CAS authentication)
    - gd (picture generation)
    - imap (mail collector and users authentication)
    - ldap (users authentication)
    - openssl (encrypted communication)


## Download

See :
* [releases](https://github.com/glpi-project/glpi/releases) for tarball packages.
* [Remi's RPM repository](http://rpms.remirepo.net/) for RPM packages (Fedora, RHEL, CentOS)


## Documentation

Here is a [pdf version](https://forge.glpi-project.org/attachments/download/1901/glpidoc-0.85-en-partial.pdf).
We are working on a [markdown version](https://github.com/glpi-project/doc)

* [Installation](http://glpi-project.org/spip.php?article61)
* [Update](http://glpi-project.org/spip.php?article172)


## Additional resources

* [Official website](http://glpi-project.org)
* [Demo](http://demo.glpi-project.org/)
* [Translations on transifex service](https://www.transifex.com/glpi/public/)
* [Issues](https://github.com/glpi-project/glpi/issues)
* [Suggestions](http://suggest.glpi-project.org)
* [Forum](http://forum.glpi-project.org)
* IRC : irc://irc.freenode.org/glpi
* [Plugin directory](http://plugins.glpi-project.org)
* [Plugin development documentation](https://github.com/pluginsGLPI/example

## 추가사항(161202)

Plugin 설치

* mydashboard -> 성공
* archires -> 버전이 안맞음 (버전속임)
* report -> 버전이 안맞음 (버전속임)
* More Reporting -> 성공
* datainjection -> 성공
* pdf -> 버전안맞음 (버전속임)
* addressing -> 버전안맞음 (버전속임)
* monitoring -> 버전안맞음 (버전속임)

한글 번역 수정

Google 계정연동
* 계정 정보는 가져오지만 session이 끊겨서 로그인은 되지않음(수정필요)

Asset에 사진 추가
* 기본 기능인 문서 연결을 이용
* 문서 타입이 img일 경우에만 view창이 폼에 띄어진다

DB정보 (현재 지운상태 입력해야지 정상작동됨)
* config/config_db
* Asset의 각종 물품의 폼에 연결된 class들에도 db정보를 입력해주어야함 (ex. computer -> inc/computer.class.php)

UTF-8로 설정 euc-kr로 설정시 한글이 다깨짐
