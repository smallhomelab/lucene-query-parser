# Lucene Query Parser

Lucene query string parser to be used as web api query or filter string. 
Base code is come from https://github.com/ralphschindler/basic-query-filter


Example queries in this language:

- `name: apple`
- `price: > 100`
- `price: > 100 AND active: = 1`
- `product.price: > 100 AND category.id: = 7`
- `name:=~ "Foo%"`
- `created_at: > "2017-01-01" and created_at: < "2017-01-31"`
- `status:= 1 AND (name:= "PHP Rocks" || name:= "I ♥ API's")`

## Install

```
composer require "smallhomelab/lucene-query-parser"
```

## Usage

```php
$parseTree = (new LucenenQueryParser\Parser)->parse($filter);

// Getting String
$str = $parseTree->toString();

// Getting Array
$arr = $parseTree->toArray();

// Getting Simple Array
$arrSimple = $parseTree->toSimpleArray();
```
