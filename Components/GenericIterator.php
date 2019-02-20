<?php
/** An iterator.
 */
interface GenericIterator
{
    /** Fetch and return the next element of the iterator.
     * If no more element is available, return null.
     */
    public function next();

    /** Return the number of element of the iterator.
     */
    public function total();

    /** True if the current element is the first element.
     */
    public function first();

}
