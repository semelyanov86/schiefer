<?php
class PBXManager_InRelation_View extends Vtiger_RelatedList_View {

	function process( Vtiger_Request $request ) {
		$orderBy = $request->get('orderby');
		if (empty($orderBy) || $orderBy == "") {
			$request->set('orderby', 'starttime');
			$request->set('sortorder', 'DESC');
		}
		return parent::process($request);
	}
}