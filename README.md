# Pointer-Objects

> An experimental structure where each object points to the next, which in turn points to the next, and so on.

## Usage
```php
//constructing the map object
$PM = new PointerMap(["name" => "Bob", 
                      "surname" => "Roberts", 
                      "specs"=> new PointerMap(["age" => 99.9, 
                                                "date of birth" => "1920/09/12"])
                     ]);

//adding/pushing a key-value pair
$PM->push("countries visited", new PointerMap("locations" => new PointerMap("Turkey", 
                                                                            "Nigeria", 
                                                                            "Tasmania", 
                                                                            "Brazil"))) ;
$PM->push("ID", 109992991656536);

//removing/popping a key-value pair
$PM->pop() ;

//getting a value
$visited = $PM->get("countries visited") ;

//getting a value inside another value
$visited = $PM->get("countries visited", "locations") ;

//setting a value
$newValue = "Morocco";
$PM->set($newValue, "countries visited", "locations", "0") ; // changes "Turkey" to "Morocco"

//setting a value with a reference (&)
$visited = &$PM->get("countries visited", "locations") ;
$visited[0] = "Morocco"; // changes "Turkey" to "Morocco"
