# Contributing

If you would like to create a new type of block here an example of module structure in `PHP`.

```
code/
└───blocks/
│   └───models/
│   └───extensions/
│   └───fields/
│   └───admins/
│   └───pages/
```

create a new file `code/blocks/your-block-name/models/YourBlockName.php`

```php
    class YourBlockName extends BaseBlock {
        
        /**
         * @return string
         */
        public function singular_name() {
            return _t('YourBlockName.SINGULARNAME', 'Your Block Name');
        }
    
        /**
         * @return string
         */
        public function plural_name() {
            return _t('YourBlockName.PLURALNAME', 'Your Block Names');
        }
            
    }
```

For Javascript and SASS (CSS). First run `npm install` to download all dependencies. Next - run `gulp` for one time build or `gulp watch` to watch your files changes.

```
src/
└───javascript/
│   └───modules/
│   │   │   YourModule.js
│   └───functions/
│   │   │   ExtraFunction.js
│   │   │   CommonFunction.js
│   │   │   Etc.js
│   │   YourModuleName.js
```

Where `YourModuleName.js` use ES6 to import your modules. An example of `YourModuleName.js`. Or just check out `maps-backend.js` for example.

```javascript
    import YourModule from './modules/YourModule';
    
    // some action within YourModuleName
    // ...
    
    const MyModule = new YourModule();
    
    MyModule.init(); // an example.
    
    // ./modules/YourModule.js
    class YourModule {
        constructor(container) {
            this.container = container;
        }
    }
    
    export default YourModule;
```

Add your styles (SASS) to `src/styles/blocks/_your-module-name.scss` and implement it to `src/styles/app.scss`

```CSS
    @import "blocks/your-module-name.scss";
```

When you finish your module of block add new documentation to `docs/YOUR_MODULE_NAME.md`.