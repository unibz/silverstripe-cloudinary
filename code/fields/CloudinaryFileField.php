<?php

class CloudinaryFileField extends FormField
{
	private $children = null;
	private $objectID = 0;

	public $FileType = 'File';

	public function __construct($name, $title = null, $value = "", $form = null) {

		$file = singleton('CloudinaryFile');
		$frontEndFields = $file->getFrontEndFields();
		foreach($frontEndFields as $field)
		{
			if ($field->getName() == 'URL') {
				$field->addExtraClass('_js-cloudinary-url');
			} else {
				$field->addExtraClass('_js-attribute');
			}

			if (in_array($field->getName(), array('FileSize', 'Format'))) {
				$field->addExtraClass('show_field');
			}

			if(in_array($field->getName(), array('FileDescription'))) {
				$field->HiddenFileField = true;
			} else if ($field->getName() == 'URL') {
				$field->CommonField = true;
			}

			$field->setName($name . "[" . $field->getName() . "]");
		}


		$this->children = $frontEndFields;
		$this->children->push(new HiddenField($name . "[ObjectID]"));

		parent::__construct($name, $title, $value);
	}

	public function getChildren() {
		return $this->children;
	}

	public function getURLField() {
		$isRaw = $this->isRaw();
		return $this->children->dataFieldByName($this->getName() . "[URL]")->setAttribute('data-is-raw', "$isRaw");
	}

    public function DataFields() {
        $dataFields = new FieldList();
        $record = $this->getForm()->getRecord();
        $thisFieldName = $this->Name;
        $thisField = $record->$thisFieldName();

		foreach($this->getChildren() as $field){
            $fieldName = $field->getName();
            preg_match('/\[(\S+)\]/', $fieldName, $matches);
            $fieldName = $matches[1];
            if(strpos($fieldName, 'URL') === false){
                if ($thisField->$fieldName) {
                    $field->setValue($thisField->$fieldName);
                }
                $dataFields->push($field);
            }
        }
        return $dataFields;
    }

    public function Field($properties = array()) {
        Requirements::css('cloudinary/css/CloudinaryFileField.css');
        Requirements::javascript('cloudinary/javascript/thirdparty/imagesloaded.js');
        Requirements::javascript('cloudinary/javascript/thirdparty/jquery.cloudinary.js');
        Requirements::javascript('cloudinary/javascript/CloudinaryFileField.js');
        // $this->children->fieldByName($this->Name . '[ObjectID]')->setValue($this->objectID);
        return $this->renderWith('CloudinaryFileField');
    }

	public function CloudName() {
		return CloudinaryUtils::cloud_name();
	}

	public function ApiKey() {
		return CloudinaryUtils::api_key();
	}

	public function isPopuplated() {
		return $this->objectID !== 0;
	}

    protected function getSubFields()
    {
        return array(
            'URL',
            'Caption',
            'Credit',
            'FileSize',
            'ObjectID'
        );
    }

    public function setValue($value, $record = null) {
        if(empty($value) && $record){
            if(($record instanceof DataObject) && $record->hasMethod($this->getName())) {
                $data = $record->{$this->getName()}();
                if($data && $data->exists()){
                    foreach ($this->getSubFields() as $fieldName) {
                        if($data->$fieldName) {
                            $this->children->dataFieldByName($this->getName() . '[' . $fieldName . ']')->setValue($data->$fieldName);
                        }
                    }
                    $this->objectID = $data->ID;
                }
            }
        }

        return parent::setValue($value, $record);
    }

    public function saveInto(DataObjectInterface $record) {
        if($this->name) {
            $value = $this->dataValue();

            $className = preg_replace('/(\w+)Field$/', '$1', get_class($this));
            $reflectionClass = new \ReflectionClass($className);
            $file = null;
            if($value['ObjectID']){
                $file = call_user_func(array($className, 'get'))->byID($value['ObjectID']);
            }
            if(!$file){
                $file = $reflectionClass->newInstance();
            }

            if($value['URL']){
                $cloudinaryUrl = $value['URL'];
                $file->URL = $cloudinaryUrl;
                $file->Format = CloudinaryUtils::file_format($value['URL']);
                $file->FileSize = $value['FileSize'];
                $this->updateFileBeforeSave($file, $value, $record);
                $file->write();

                $record->setCastedField($this->name . 'ID', $file->ID);
            } else {
                if ($file->exists()) {
                    $file->delete();
                }

                $record->setCastedField($this->name . 'ID', 0);
            }
        }
    }

    protected function updateFileBeforeSave(CloudinaryFile &$file, &$value = array(), DataObjectInterface &$record)
    {
        $file->FileTitle = $value['FileTitle'];
        $file->FileDescription = $value['FileDescription'];
    }

	public function IsRaw()
	{
		if($field = $this->children->dataFieldByName($this->getName() . "[URL]")) {
			return CloudinaryUtils::resource_type($field->value) == 'raw';
		}
	}
}
