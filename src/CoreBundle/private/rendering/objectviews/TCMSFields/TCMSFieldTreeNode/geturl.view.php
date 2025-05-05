            if (empty($this-><?php echo $aFieldData['sFieldName']; ?>)) {
                return null;
            }
            try {
                $page = self::getPageService()->getByTreeId($this-><?php echo $aFieldData['sFieldName']; ?>);
                if (null === $page) {
                    return null;
                }
                $language = null;
                if ($forcePageLanguage && !empty($page->fieldCmsLanguageId)) {
                    $language = TdbCmsLanguage::GetNewInstance($page->fieldCmsLanguageId, $this->GetLanguage());
                }
                if ($bForceDomain) {
                    return self::getPageService()->getLinkToPageObjectAbsolute($page, array(), $language);
                } else {
                    return self::getPageService()->getLinkToPageObjectRelative($page, array(), $language);
                }
            } catch (Symfony\Component\Routing\Exception\RouteNotFoundException $e) {
                return null;
            }
