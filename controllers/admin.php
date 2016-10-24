<?php

/**
 * Allow administrators to change settings for the plug-in.
 *
 * Class SPODPRIVACY_CTRL_Admin
 */
class SPODPRIVACY_CTRL_Admin extends ADMIN_CTRL_Abstract
{
	public function settings($params)
	{
		$this->setPageTitle(OW::getLanguage()->text('spodprivacy', 'settings_title'));
		$this->setPageHeading(OW::getLanguage()->text('spodprivacy', 'settings_heading'));

		$form = new Form('settings');
		$this->addForm($form);

		/* spodprivacy_filter */
		$frmFilter = new Textarea('filter');
		$preference = BOL_PreferenceService::getInstance()->findPreference('spodprivacy_filter');
		$filter = empty($preference) ? "" : $this->decodeFilter( $preference->defaultValue );
		$frmFilter->setLabel(OW::getLanguage()->text('spodprivacy', 'label_filter'));
		$frmFilter->setValue($filter);
		$frmFilter->setDescription(OW::getLanguage()->text('spodprivacy', 'description_filter'));
		$form->addElement($frmFilter);

		/* spodprivacy_debug */
		$frmDebug = new CheckboxField('debug');
		$preference = BOL_PreferenceService::getInstance()->findPreference('spodprivacy_debug');
		$debug = empty($preference) ? false : $preference->defaultValue;
		$frmDebug->setLabel(OW::getLanguage()->text('spodprivacy', 'label_debug'));
		$frmDebug->setValue($debug);
		$frmDebug->setDescription(OW::getLanguage()->text('spodprivacy', 'description_debug'));
		$form->addElement($frmDebug);

		/* Submit button */
		$submit = new Submit('save');
		$submit->setValue(OW::getLanguage()->text('spodprivacy', 'preferences_submit'));
		$form->addElement($submit);

		if ( OW::getRequest()->isPost() && $form->isValid($_POST))
		{
			$data = $form->getValues();

			/* spodprivacy_filter */
			$preference = BOL_PreferenceService::getInstance()->findPreference('spodprivacy_filter');

			if (empty($preference))
			{
				$preference = new BOL_Preference();
			}

			$preference->key = 'spodprivacy_filter';
			$preference->sectionName = 'general';
			$preference->defaultValue = $this->encodeFilter( $data['filter'] );
			$preference->sortOrder = 1;
			BOL_PreferenceService::getInstance()->savePreference($preference);

			/* spodprivacy_debug */
			$preference = BOL_PreferenceService::getInstance()->findPreference('spodprivacy_debug');

			if (empty($preference))
			{
				$preference = new BOL_Preference();
			}

			$preference->key = 'spodprivacy_debug';
			$preference->sectionName = 'general';
			$preference->defaultValue = $data['debug'];
			$preference->sortOrder = 1;
			BOL_PreferenceService::getInstance()->savePreference($preference);
		}
	}

	// Encode user input to json for faster decoding
	private function encodeFilter( $data )
	{
		if ( empty( $data ) )
		{
			return '';
		}

		$json = [];
		$v = explode( "\n", $data );
		foreach ( $v as $row )
		{
			$row = trim( $row );
			list( $controller, $actions ) = explode( ':', $row );
			if ( ! isset( $json[ $controller ] ) )
			{
				$json[ $controller ] = array();
			}
			$actions = explode( ',', $actions );
			foreach ( $actions as $action )
			{
				if ( ! in_array( $action, $json[ $controller ] ) )
				{
					$json[ $controller ][] = $action;
				}
			} // foreach ( $actions as $action )
		} // foreach ( $v as $row )
		return json_encode( $json );
	}

	// Decode json to user-readable text for easier editing
	private function decodeFilter( $json_text )
	{
		if ( empty( $json_text ) )
		{
			return '';
		}

		$json = json_decode( $json_text );

		$data = array();
		foreach ( $json as $controller => $actions )
		{
			$data[] = $controller . ':' . join( ',', $actions );
		}
		return join( "\n", $data );
	}

}