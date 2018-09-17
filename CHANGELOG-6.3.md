CHANGELOG for 6.3.x
===================

# Changed Features

## Variable Replacement

Post render variables are no longer tied to the ChameleonController. Replacement is now performed in 
`\ChameleonSystem\CoreBundle\Response\ResponseVariableReplacerInterface` and may therefore be called independent from
the controller.
