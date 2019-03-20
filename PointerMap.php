<?php

    /**
     *
     * @author j. bradley briggs
     */
    class PointerMap
    {
        private $successor = NULL;
        private $predecessor = NULL ;
        private $key = NULL ;
        private $obj = NULL;
        private $index = 0 ; //reluctantly added, but much needed

        function __construct($map)
        {
            if ($map) // move through each key-value pair in the object
            {
                foreach ($map as $key=>$object)
                {
                   $this->push($key, $object) ;
                }
            }
        }

        /**
         * Pushes a key-value pair to the end of the map.
         */
        function push($key, $object)
        {
            if ($this->obj === NULL && $this->key === NULL) // the first key-value that gets assigned
            {
                $this->key = $key ;
                $this->obj = $object ;
            }
            else // every successive key-value gets pushed here
            {
                if (!$this->hasSuccessor()) // if there is NO successor, i.e. if this is the LAST item in the map
                {
                    $this->successor = new PointerMap([$key => $object]) ; // create a link forwards
                    $this->successor->predecessor = $this ; // create a link backwards
                    $this->successor->index = $this->index + 1 ;
                }
                else $this->successor->push($key, $object) ; // if there is a successor, pass this on to that successor where it will also be passed on, and so on
            }
            return $this ; // return the instance so we can chain pushes together if need be
        }

        /**
         * Removes the LAST item of the map and returns it.
         */
        function pop()
        {
            if ($this->hasSuccessor()) return $this->successor->pop() ; // if there is a successor, pass the action along to it and so on - until we reach the final item
            else // if there is no successor (i.e. this is the last item)
            {
                $this->predecessor->successor = NULL ; // remove the forward link to this instance
                $object = $this->obj ;
                $this->obj = NULL ;
                $this->key = NULL ;
                $this->predecessor = NULL ;
                return $object ;
            }
        }

        function hasSuccessor()
        {
            return $this->successor != NULL ;
        }

        function hasPredecessor()
        {
            return $this->predecessor != NULL ;
        }

        private function toString()
        {
            if (!$this->hasSuccessor()) return $this->key.": ".$this->obj ; // if there is no successor, simply return  key: value
            else return $this->key.": ".$this->obj.", ".$this->successor->toString() ;
        }

        function __toString()
        {
            return "[".$this->toString()."]" ;
        }

        /**
         * Returns the last object in the map. Note* the reference to this object is
         * also returned, so modifying the object will also modify the object in the map. 
         */
        function &getLast()
        {
            if ($this->hasSuccessor()) return $this->successor->getLast() ;
            else return $this->obj ;
        }

        /**
         * Returns the first object in the map. Note* the reference to this object is
         * also returned, so modifying the object will also modify the object in the map. 
         */
        function &getFirst()
        {
            if ($this->hasPredecessor()) return $this->predecessor->getFirst() ;
            else return $this->obj ;
        }

        function &getObject($key)
        {
            if ($key === $this->key) return $this->obj ;
            else if ($this->hasSuccessor()) return $this->successor->getObject($key) ;
            else // key does not exist, so create it 
            {
                if ($this->key === NULL) 
                {
                    $this->key = $key ;
                    return $this->obj ;
                }
                else 
                {
                    $this->push($key, NULL) ;
                    return $this->successor->obj ;
                }
            }
            /*throw new Exception("Key \".($key).\" does not exist.") ;*/
        }

        /**
         * Gets a value from an index. If the index is negative, the function will move
         * backwards through the map. For example, setting $index = -1, will return the last item
         * in the map, -2 will return the second last and so on.
         */
        function getFromIndex($index)
        {
            $mapSize = $this->size() ;
            if ($mapSize != 0)
            {
                // modulate the index
                    if ($index < 0) $index += $mapSize*ceil(abs($index)/$mapSize) ;
                    $index %= $mapSize ;
                return $this->getObjectFromIndex($index) ;
            }
        }

        private function getObjectFromIndex($index)
        {
            if ($index > $this->index /*&& $this->hasSuccessor()*/) return $this->successor->getObjectFromIndex($index) ;
            //else if (!$this->hasSuccessor()) return NULL ;
            else return $this->obj ;
        }

        /**
         * Gets the number of items in the map.
         */
        function size()
        {
            $count = 0 ;
            $this->stepThrough($count) ;
            return $count ;
        }

        /**
         * Step through the map, each time incrementing the count
         */
        private function stepThrough(&$count)
        {
            $count++;
            if ($this->hasSuccessor()) $this->successor->stepThrough($count) ;
        }

        /**
         * Checks whether a key exists in the the map
         */
        function keyExists($key)
        {
            if ($key === $this->key) return true ;
            else if (hasSuccessor()) return $this->successor->keyExists($key) ;
            else return false ;
        }

        /**
         * Gets the object at a particular key path. Note* This also returns the reference, so modifying 
         * the object will also modify the map. 
         * */    
        function &get(...$keyPath)
        {
            if ($keyPath)
            {
                $outer = &$this ;
                $keyCount = 0 ;
                foreach ($keyPath as $key)
                {
                    $inner = &$outer->getObject($key) ;
                    if ($inner instanceof self && $keyCount+1 < count($keyPath))
                    {
                        $outer = &$inner ;
                    }
                    else return $inner ;

                    $keyCount++ ;
                }
            }
        }

        /**
         * Sets the object at the particular key path. If the keys don't exist, they will be created
         * along the way. Note* any existing values will be overwritten
         */
        function set($object, ...$keyPath)
        {
            $obj = &$this->get(...$keyPath) ;
            $obj = $object ;
            return $this ;
        }

        function newKey($key)
        {
            $pattern = "/(^[\D]+)\s?(\d*)/" ;
            $doesMatch = preg_match($pattern, $key, $matches) ;
            /*
                match 0: full key
                match 1: key only 
                match 2: number
            */
            if ($doesMatch == 1)
            {
                //return $matches ;
                echo $matches[1]."<br/>" ;
                $keyCount = $matches[2] ;
                if ($keyCount == "") $keyCount = 0 ;
                //if (mb_strcut($key, ))
                return $matches[1].($keyCount+1) ;
            }
            else return NULL ;
        }

    }
/*
    $PM = new PointerMap(["name" => "Bob", "surname" => "Hoskins", "specs" => new PointerMap(["Age" => "99", "Date of birth" => "1920/12/5"])]) ;
    $PM->push("ID", "99109119119900");
    $PM->push("war record", new PointerMap(["combat experience"=> "28 years", "locations"=> new PointerMap(["Bosnia", "Vietnam", "Desert Storm"])])) ;

    echo $PM."<br/>" ;
    $exp = &$PM->get("war record", "combat experience") ;
    echo $exp."<br/>" ;
    $exp = "110 years" ;
    echo $PM."<br/>" ;

    $locations = &$PM->get("war record", "locations") ;
    echo $locations."<br/>" ;
    $locations = new PointerMap(["Verdun", "Shanghai", "Istanbul", "Vietnam", "St. Pietersburg"]) ;
*/
    /*$name = &$PM->get("name") ;
    echo $name."<br/>" ;
    $name = "rb" ;*/
/*
    $PM->set("Robert", "name") ;
    $PM->set("2013/09/18", "specs", "Date of birth") ;

    echo "SIZE = ".$PM->size() ;

   echo "<br/>".$PM."<br/>" ;
   echo $PM->getFromIndex(1) ; // [0, 5, -5, -10] [1, 6, -4, -9] [2, 7, -3, -8] [3, 8, -2, -7] [4, 9, -1, -6]*/
?>