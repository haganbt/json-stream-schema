<?php
require 'lib/datasift.php';
if (function_exists('date_default_timezone_set')) {
	date_default_timezone_set('UTC');
}





class EventHandler implements DataSift_IStreamConsumerEventHandler
{
  	
	protected $master = array();
	
	public function getMaster(){
		return $this->master;
	}
	
	
    public function onInteraction($consumer, $interaction, $hash)
    {
    	  
	  try{
		
		if(is_array($interaction) === true && !empty($interaction)){
			$this->master =  array_merge_recursive_distinct($this->master, $interaction);	
		}
		
		echo json_encode($this->master) . "\n";		
		
	  } catch (Exception $e){
		 throw new Exception( 'ERROR:', 0, $e);
	  }

}



    // Ignore the other events for the purposes of this example.
    public function onConnect($consumer)                      { }
    public function onDeleted($consumer, $interaction, $hash) { }
    public function onStatus($consumer, $type, $info)         { }
    public function onWarning($consumer, $message)            { }
    public function onError($consumer, $message)              { }
    public function onDisconnect($consumer)                   { }
    public function onStopped($consumer, $reason)             { }
  }

  // Create the user
  $user = new DataSift_User('<user>', '<api_key>');
  // Create a definition looking for the word "datasift"
  $def = $user->createDefinition('interaction.sample < 0.1');
  // Get an HTTP stream consumer for that definition
  $eventHandler = new EventHandler();
  $consumer = $def->getConsumer(DataSift_StreamConsumer::TYPE_HTTP, $eventHandler);
  // Consume it - this will not return unless the stream gets disconnected
  $consumer->consume();

  $master = $eventHandler->getMaster();


  
  
  
  
  
/*
 * array_merge_recursive does indeed merge arrays, but it converts values with duplicate
 * keys to arrays rather than overwriting the value in the first array with the duplicate
 * value in the second array, as array_merge does. I.e., with array_merge_recursive,
 * this happens (documented behavior):
 *
 * array_merge_recursive(array('key' => 'org value'), array('key' => 'new value'));
 *     => array('key' => array('org value', 'new value'));
 *
 * array_merge_recursive_distinct does not change the datatypes of the values in the arrays.
 * Matching keys' values in the second array overwrite those in the first array, as is the
 * case with array_merge, i.e.:
 *
 * array_merge_recursive_distinct(array('key' => 'org value'), array('key' => 'new value'));
 *     => array('key' => array('new value'));
 *
 * Parameters are passed by reference, though only for performance reasons. They're not
 * altered by this function.
 *
 * @param array $array1
 * @param array $array2
 * @return array
 * @author Daniel <daniel (at) danielsmedegaardbuus (dot) dk>
 * @author Gabriel Sobrinho <gabriel (dot) sobrinho (at) gmail (dot) com>
 */
function array_merge_recursive_distinct ( array &$array1, array &$array2 )
{
  $merged = $array1;

  foreach ( $array2 as $key => &$value )
  {
    if ( is_array ( $value ) && isset ( $merged [$key] ) && is_array ( $merged [$key] ) )
    {
      $merged [$key] = array_merge_recursive_distinct ( $merged [$key], $value );
    }
    else
    {
      if(isset($merged[$key]) && count($merged[$key]) > 3  ){
      	continue;
      }	
		
      $merged[$key] = $value;
    }
  }

  return $merged;
}
?>