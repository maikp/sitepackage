.. include:: /Includes.rst.txt

=====
Usage
=====

Using the Form Editor
=====================

The easiest way to add a Multi Image Upload field:

1. Open **Web > Forms** in the TYPO3 backend
2. Create a new form or edit an existing one
3. Click **"Create new element"**
4. Select **"Multi Image Upload"** from the **"Custom"** group
5. Configure the element properties in the inspector panel

Configure the element:

- **Label**: The field label shown to users
- **Save to file mount**: Where uploaded files are stored (e.g., ``1:/user_upload/``)
- **Allowed MIME types**: Which file types are accepted

Using YAML Configuration
========================

For version-controlled form definitions, use YAML:

.. code-block:: yaml

   type: Form
   identifier: contact-form
   label: 'Contact Form'
   prototypeName: standard

   renderables:
     - type: Page
       identifier: page-1
       label: 'Contact'
       renderables:
         - type: Text
           identifier: name
           label: 'Your Name'
           validators:
             - identifier: NotEmpty

         - type: Text
           identifier: email
           label: 'Email Address'
           validators:
             - identifier: NotEmpty
             - identifier: EmailAddress

         - type: MultiImageUpload
           identifier: attachments
           label: 'Attach Images'
           properties:
             saveToFileMount: '1:/user_upload/'
             allowedMimeTypes:
               - 'image/jpeg'
               - 'image/png'

   finishers:
     - identifier: EmailToReceiver
       options:
         implementationClassName: BrezoIt\MultiFileUpload\Form\Finishers\MultiFileEmailFinisher
         subject: 'New Contact Form Submission'
         recipients:
           info@example.com: 'Info'
         senderAddress: '{email}'
         senderName: '{name}'
         attachUploads: true

User Experience
===============

When users interact with the form:

1. **Upload**: Click the file input and select multiple images
2. **Preview**: Uploaded images appear in a responsive grid with thumbnails
3. **Delete**: Each image has a checkbox to mark it for removal
4. **Submit**: On form submission, selected files are attached to the email

The preview gallery uses:

- Responsive Bootstrap 5 grid layout
- Card-based design for each image
- Lightbox support for full-size preview
- Clear delete checkboxes with labels

Template Customization
======================

Override the default templates by creating custom YAML configuration:

.. code-block:: yaml

   TYPO3:
     CMS:
       Form:
         prototypes:
           standard:
             formElementsDefinition:
               MultiImageUpload:
                 renderingOptions:
                   templateRootPaths:
                     100: 'EXT:your_extension/Resources/Private/Templates/Form/'
                   partialRootPaths:
                     100: 'EXT:your_extension/Resources/Private/Partials/Form/'

The main templates are:

- ``Partials/Form/MultiImageUpload.html`` - Main field rendering
- ``Partials/Form/SummaryPage.html`` - Summary page display

Troubleshooting
===============

**Files not uploading**

- Check that the upload folder exists and is writable
- Verify ``saveToFileMount`` points to a valid FAL storage
- Ensure MIME types are correctly configured

**Files not attached to email**

- Use ``MultiFileEmailFinisher`` instead of standard ``EmailFinisher``
- Set ``attachUploads: true`` in finisher options

**Preview images not showing**

- Clear TYPO3 caches after configuration changes
- Check browser console for JavaScript errors
- Verify CSS is loaded correctly
