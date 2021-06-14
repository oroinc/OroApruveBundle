The upgrade instructions are available at [Oro documentation website](https://doc.oroinc.com/backend/setup/upgrade-to-new-version/).

The current file describes significant changes in the code that may affect the upgrade of your customizations.

## 4.2.0 (2020-01-29)
[Show detailed list of changes](incompatibilities-4-2.md)

## 4.1.0 (2020-01-31)

### Removed
* `*.class` parameters for all entities were removed from the dependency injection container.
The entity class names should be used directly, e.g. `'Oro\Bundle\EmailBundle\Entity\Email'`
instead of `'%oro_email.email.entity.class%'` (in service definitions, datagrid config files, placeholders, etc.), and
`\Oro\Bundle\EmailBundle\Entity\Email::class` instead of `$container->getParameter('oro_email.email.entity.class')`
(in PHP code).

## 4.0.0-rc (2019-05-29)
[Show detailed list of changes](incompatibilities-4-0-rc.md)

## 4.0.0-beta (2019-03-28)
### Changed
* In `Oro\Bundle\AuthorizeNetBundle\Controller\Frontend\PaymentProfileController::deleteAction` 
 (`oro_authorize_net_payment_profile_frontend_delete` route)
 action the request method was changed to DELETE. 
* In `Oro\Bundle\AuthorizeNetBundle\Controller\SettingsController::checkCredentialsAction` 
 (`oro_authorize_net_settings_check_credentials` route)
 action the request method was changed to POST. 

## 1.6.0 (2018-01-31)
[Show detailed list of changes](incompatibilities-1-6.md)

## 1.4.0 (2017-09-29)
[Show detailed list of changes](incompatibilities-1-4.md)

## 1.3.0 (2017-07-28)
[Show detailed list of changes](incompatibilities-1-3.md)
### Changed
- hide loading mask only when apruve popup is fully loaded
## 1.2.2 (2017-06-26)
### Fixed
- added ability to press on any element in iClickButtonInEmbed
