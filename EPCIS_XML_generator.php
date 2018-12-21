<?php

$data = array(

	'sender' 	=> '1111111111',
	'receiver'	=> '2222222222',
	'events' => array(
		array(
			'class' 	=> 'object',
			'bizstep' 	=> 'commissioning',
			'action'	=> 'ADD',
			'epcList'	=> []
		),
		array(
			'class' 		=> 'aggregation',
			'bizstep' 		=> 'packing',
			'action'		=> 'ADD',
			'parent_id' 	=> 'urn:epc:id:sscc:0346672.0000000010',
			'child_epcs'	=> ['urn:epc:id:sgtin:0369499.033060.100000000200', 
								'urn:epc:id:sgtin:0369499.033060.100000000300', 
								'urn:epc:id:sgtin:0369499.033060.100000000400']

		)


	)

);

class Event{

	protected $eventTime;
	protected $action;
	protected $bizStep;
	protected $disposition;

}



class ObjectEvent extends Event{

	private $epcList;
	private $objEvent;

	public function __construct($event_data){

		$obj_xml = new SimpleXMLElement('<ObjectEvent></ObjectEvent>');
		$obj_xml->addChild('eventTime', '2018-01-05T23:09:48.444Z');
		$this->objEvent = $obj_xml;
	}


	public function toString(){
		return $this->objEvent->asXML();
	}



}



class AggregationEvent extends Event{
	private $parentID;
	private $childEPCs;
}



class EPCIS{

	private $data;
	public 	$header;
	public 	$body;
	public 	$doc;



	public function __construct(array $data){

		$this->data = $data;
		$creation_date = '';

		$format_xmlstring = 
			'<?xml version="1.0" encoding="UTF-8"?>
			<epcis:EPCISDocument
			    xmlns:tl="http://epcis.tracelink.com/ns"
			    xmlns:sbdh="http://www.unece.org/cefact/namespaces/StandardBusinessDocumentHeader"
			    xmlns:cbvmda="urn:epcglobal:cbv:mda"
			    xmlns:epcis="urn:epcglobal:epcis:xsd:1" schemaVersion="1.2" creationDate="2018-01-11T16:42:54">
			    <EPCISHeader>
			        <sbdh:StandardBusinessDocumentHeader>
			            <sbdh:HeaderVersion>1.0</sbdh:HeaderVersion>
			            <sbdh:Sender>
			                <sbdh:Identifier Authority="GLN">%s</sbdh:Identifier>
			            </sbdh:Sender>
			            <sbdh:Receiver>
			                <sbdh:Identifier Authority="GLN">%s</sbdh:Identifier>
			            </sbdh:Receiver>
			        </sbdh:StandardBusinessDocumentHeader>
			    </EPCISHeader>
			    <EPCISBody><EventList></EventList></EPCISBody>
			</epcis:EPCISDocument>';

		$xmlstring = sprintf($format_xmlstring, $data['sender'], $data['receiver']);
		$this->doc = simplexml_load_string($xmlstring);
		$this->create_events();
	}



	private function create_events(){
		foreach($this->data['events'] as $event_data){
			if( $event_data['class'] == 'object'){
				$objEvent = new ObjectEvent($event_data);
				echo $objEvent->toString();
			}elseif( $event_data['class'] == 'aggregation'){
				$objEvent = new AggregationEvent($event_data);
			}
		}
	}



	public function toString(){
		return $this->doc->asXML();
	}
}

$epcis = new EPCIS($data);
echo $epcis->toString();




?>