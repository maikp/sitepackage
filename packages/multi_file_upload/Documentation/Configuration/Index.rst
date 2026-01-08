.. include:: /Includes.rst.txt

=============
Configuration
=============

Form Element Properties
=======================

The Multi Image Upload element supports the following properties:

.. t3-field-list-table::
   :header-rows: 1

   - :Property:        Property
     :Type:            Type
     :Default:         Default
     :Description:     Description

   - :Property:        saveToFileMount
     :Type:            string
     :Default:         ``1:/user_upload/``
     :Description:     FAL storage path for uploaded files

   - :Property:        allowedMimeTypes
     :Type:            array
     :Default:         ``image/jpeg, image/png, image/bmp``
     :Description:     List of allowed MIME types

   - :Property:        imageMaxWidth
     :Type:            int
     :Default:         ``400``
     :Description:     Maximum width for preview images (pixels)

   - :Property:        imageMaxHeight
     :Type:            int
     :Default:         ``400``
     :Description:     Maximum height for preview images (pixels)

   - :Property:        imageLinkMaxWidth
     :Type:            int
     :Default:         ``1200``
     :Description:     Maximum width for lightbox images (pixels)

Rendering Options (CSS Classes)
===============================

Customize the appearance by overriding these CSS classes:

.. t3-field-list-table::
   :header-rows: 1

   - :Option:          Option
     :Default:         Default
     :Description:     Description

   - :Option:          previewListClass
     :Default:         ``row g-3``
     :Description:     Container class for the image grid

   - :Option:          previewItemClass
     :Default:         ``col-md-4 col-lg-3 mb-3``
     :Description:     Class for each image item in the grid

   - :Option:          previewImageWrapperClass
     :Default:         ``card``
     :Description:     Wrapper class for image card

   - :Option:          deleteWrapperClass
     :Default:         ``form-check card-footer``
     :Description:     Class for delete checkbox wrapper

   - :Option:          summaryItemClass
     :Default:         ``col-6 col-md-4 col-lg-3 mb-2``
     :Description:     Class for items on summary page

YAML Configuration Example
==========================

Full example with all options:

.. code-block:: yaml

   renderables:
     - identifier: images
       type: MultiImageUpload
       label: 'Upload Images'
       properties:
         saveToFileMount: '1:/user_upload/'
         allowedMimeTypes:
           - 'image/jpeg'
           - 'image/png'
           - 'image/webp'
         imageMaxWidth: 400
         imageMaxHeight: 400
         imageLinkMaxWidth: 1200
       renderingOptions:
         previewListClass: 'row g-3'
         previewItemClass: 'col-md-4 col-lg-3 mb-3'
         previewImageWrapperClass: 'card'
         deleteWrapperClass: 'form-check card-footer'

Email Finisher Configuration
============================

To send emails with file attachments, use the ``MultiFileEmailFinisher``:

.. code-block:: yaml

   finishers:
     - identifier: EmailToReceiver
       options:
         implementationClassName: BrezoIt\MultiFileUpload\Form\Finishers\MultiFileEmailFinisher
         subject: 'New form submission'
         recipients:
           admin@example.com: 'Administrator'
         senderAddress: 'noreply@example.com'
         senderName: 'Website Contact Form'
         attachUploads: true

The finisher automatically:

- Detects all ``MultiImageUpload`` elements in the form
- Attaches all uploaded files to the email
- Supports both HTML and plain text email formats
- Works with standard ``FileUpload`` elements as well

Two pre-configured finisher variants are available:

- ``MultiFileEmailToReceiver``: Send to site administrator
- ``MultiFileEmailToSender``: Send confirmation to form submitter

Custom Styling
==============

Override the default CSS by including your own stylesheet:

.. code-block:: typoscript

   page.includeCSS {
       multiUploadCustom = EXT:your_extension/Resources/Public/Css/multi-upload-custom.css
   }

The extension's CSS uses modern CSS nesting and is located at:
``EXT:multi_file_upload/Resources/Public/Css/multi-upload.css``
