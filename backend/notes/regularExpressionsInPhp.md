## preg_match($pattern, $src, $matches)

### intro
    the match variable is also availabe in python, and from it we can access groups as a single array of tuples, or group(int), which is the last match of the indicated group number (with overwrite)

in php working with regular expressions is similar.

1. **finding a match - preg_match($pattern, $subject, $match)**
```php
$str = "string to search from";
$pattern = "/regex pattern/";
$result = preg_match($pattern, $str, $match);
if($result === 1){
    print_r($matches);
    /*
    * Array
    * (
    *  [0] input
    *  [1] group 1 match (last in/found first out/match)
    *  .
    *  .
    *  .
    *  [n] group n match
    * )
    */
}
```
2. **string replacement - preg_replace($pattern, $replacement, $subject)**
```php
$str = "abc";
$pattern = "/(\w)/"
```