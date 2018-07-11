Configuration
=============

Adding additional substitutions for newsletter texts
----------------------------------------------------

Newsletter pages can include placeholders, that can be substituted individually on a per user basis. For example, you might want to add the user's name by placing a `[{salutation}], [{firstname}], [{lastname}]` in a text field.
The newsletter package will substitute those placeholders when rendering and/or sending the newsletter.

If you want to substitute additional placeholder in newsletters, you can implement your own `\ChameleonSystem\NewsletterBundle\PostProcessing\PostProcessorInterface` and tag it in the service
container using the tag name `chameleon_system_newsletter.post_processor`.

If you have existing extensions doing this by extending `\TCMSNewsletterCampaign` you should move your code into a new post processor, otherwise it will only work in the sent mails, but not on the
linked html version, that will be displayed in the browser.

