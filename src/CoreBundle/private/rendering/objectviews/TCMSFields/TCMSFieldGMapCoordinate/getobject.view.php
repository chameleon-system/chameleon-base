            $oMap = $this->GetFromInternalCache('oGoogleMap'.$sWidth.$sHeight.$sMapType.$iZoom.$bShowResizeBar.$bHookMenuLinks.$bShowStreetViewControl);
            if(!$oMap) {
                $oMap = null;
                if(is_array($this->sqlData) && isset($this->sqlData['<?php echo $aFieldData['sFieldDatabaseName']; ?>'])) {
                    $sCoordinates = $this->sqlData['<?php echo $aFieldData['sFieldDatabaseName']; ?>'];
                    if(!empty($sCoordinates)) {
                        $aCoordinates = explode("|",$sCoordinates);
                        if($aCoordinates[0]>0) {
                            $oMap = new TGoogleMap();
                            $oMap->setApiKey($apiKey);
                            $oMap->setMapSize($sWidth, $sHeight);
                            $oMap->setMapType($sMapType);
                            $oMap->SetZoomLevel($iZoom);
                            $oMap->showResizeBar($bShowResizeBar);
                            $oMap->hookMenueLinks($bHookMenuLinks);
                            $oMap->showStreetViewControl($bShowStreetViewControl);
                            $oMarker = new TGoogleMapMarker();
                            $oMarker->title = "";
                            $oMarker->description = "";
                            $oMarker->iconIndex = 0;
                            $oMarker->SetID();
                            $oMarker->latitude = $aCoordinates[0];
                            $oMarker->longitude = $aCoordinates[1];
                            $oMap->addMarker($oMarker);
                        }
                    }
                }
            }
            return $oMap;