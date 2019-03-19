<?php

    /**
     *
     * @author j. bradley briggs
     */
    class PointerList
    {
        private $predecessor = NULL;
        private $successor = NULL;
        private $obj = NULL;
        private $index = 0 ;

        function __construct(...$objects)
        {
            if ($objects) 
            {
                foreach ($objects as $object)
                    $this->push($object) ;
            }
        }

        /**
         * Pushes an object onto the END of the list.
         */
        function push($object)
        {
            if ($this->obj === NULL) //if this is the first value to be pushed
            {
                $this->obj = $object ;
            }
            else
            {
                if (!$this->hasSuccessor())//if there is no successor, (i.e. this is the last item in the list), add the value here 
                {
                    $this->successor = new PointerList($object) ; // create a link forwards
                    $this->successor->predecessor = $this ; // create a link backwards
                    $this->successor->index = $this->index+1 ;
                } 
                else $this->successor->push($object) ; // if there is a successor, pass this onto the successor, which will pass it on - and so on
            }
            return $this ;
        }

        /**
         * Removes the last item in the list and return it. 
         */
        function pop()
        {
            if ($this->hasSuccessor()) return $this->successor->pop() ; // if there is a successor, pass this onto the successor, which will pass it on - and so on
            else // if there is no successor, then this is the last item in the list
            {
                $this->predecessor->successor = NULL ; // remove the forward link to this instance
                $object = $this->obj ;
                $this->obj = NULL ;
                $this->predecessor = NULL ;
                $this->index = 0 ;
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

        function toString()
        {
            if (!$this->hasSuccessor()) return $this->obj ;
            else return $this->obj.", ".$this->successor->toString() ;
        }

        function __toString()
        {
            return "[".$this->toString()."]" ;
        }

        /**
         * Gets the last item in the list. Note* this also returns the reference to the object, so modifying that object
         * also modifies the list itself.
         */
        function &getLast()
        {
            if ($this->hasSuccessor()) return $this->successor->getLast() ;
            else return $this->obj ;
        }

        /**
         * Gets the first item in the list. Note* this also returns the reference to the object, so modifying that object
         * also modifies the list itself.
         */
        function &getFirst()
        {
            return $this->obj ;
        }

        /**
         * Gets the object at a particular index.
         */
        function get($index)
        {
            if ($this->hasSuccessor() && $this->index < $index) return $this->successor->get($index) ;
            else if ($index == $this->index) return $this->obj ;
            else // end of the line
            {
                if ($index > $this->index) throw new Exception("Index \".($index).\" out of bounds.") ;
                else return $this->obj ;
            }
        }

        /**
         * Set a particular object at a certain index 
         */
        function set($object, $index)
        {
            if ($this->hasSuccessor() && $this->index < $index) $this->successor->set($object, $index) ;
            else if ($index == $this->index) $this->obj = $object ;
            else // end of the line
            {
                if ($index > $this->index) throw new Exception("Index \".($index).\" out of bounds.") ;
                else $this->obj = $object ;
            }
        }

        /**
         * Gets the number of items in the list.
         */
        function size()
        {
            if ($this->hasSuccessor()) return $this->successor->size() ;
            else return $this->index+1 ;
        }

        /**
         * Reverses the order of all items in the list.
         */
        function reverse()
        {
            $size = $this->size() ;
            for ($i=$size-1; $i>=$size/2; $i--)
            {
                $this->interchange($i, $size-$i-1) ;
            }
        }

        /**
         * Interchanges two objects in the list.
         */
        function interchange($index1, $index2)
        {
            $obj1 = $this->get($index1) ;
            $obj2 = $this->get($index2) ;
            
            $this->set($obj2, $index1) ;
            $this->set($obj1, $index2) ;
        }

    }
/*
    $LL = new PointerList() ;

    $LL->push(new PointerList("hello", "there", "general", "kenobi")) ;
    $LL->push(new PointerList("you", "are", "a", "bold", "one")) ;
    $LL->push(new PointerList("wow", "this", "is", "great")) ;
    $LL->push(new PointerList(new PointerList("hundreds", "of", "millions"), "whom", "this", "may", "concern")) ;

    $LL->reverse() ;

  
    echo $LL ;
*/
?>