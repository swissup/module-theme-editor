# Theme Editor

### Installation

```bash
cd <magento_root>
composer config repositories.swissup composer https://docs.swissuplabs.com/packages/
composer require swissup/module-theme-editor --prefer-source
bin/magento module:enable Swissup_Core Swissup_ThemeEditor
bin/magento setup:upgrade
```
