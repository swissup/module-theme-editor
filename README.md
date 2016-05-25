# Theme Editor

### Installation

```bash
cd <magento_root>
composer config repositories.swissup/theme-editor vcs git@github.com:swissup/theme-editor.git
composer require swissup/theme-editor:dev-master --prefer-source
bin/magento module:enable Swissup_ThemeEditor
bin/magento setup:upgrade
```
