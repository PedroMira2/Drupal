# Mercury Editor Task

The **Mercury Editor Task** provides a dedicated 'Mercury Editor' task (tab)
to the editing experience.


## Features

Installation

- Configure 'Mercury Editor' form display mode.
- Enables 'Mercury Editor' form display for all content types that use Mercury Editor.

UI/UX

- Adds dedicated 'Mercury Editor' (a.k.a Layout) task (and route) to nodes.
- Modifies the controller for Mercury Editor entity forms to support dedicated 'Mercury Editor' route.
- Alters node content translation page to include a layout operation.
- Hides layout paragraphs builder widgets on the node edit form.
  - For the Schema.org Devel module's generate query parameter we must
    visual hide the widget to ensure all data is submitted as expected.
- Adds 'Mercury Editor' to operations when nodes that support 'Mercury Editor'.
- Adds 'Mercury Editor' to operations when node translations that support 'Mercury Editor'.
- Adds "Save and edit layout" button to node add and edit form.


## Notes

- This is a proof-of-concept for only nodes.
- Decide if this approach should support all content entity types.


## Configuration

- Go to the Mercury Editor Task Settings configuration page.
  (/admin/config/content/mercury-editor/task)
- Enter the label to display for the dedicated Mercury Editor task and operation.
- Enter the field names, field groups, and components to display via the dedicated Mercury Editor form mode.
- Check 'Update all existing Mercury Editor form modes'
- If checked, a "Save and edit layout" button will be added to the node form on Mercury Editor enabled content types. This button will redirect to Mercury Editor after saving a new node.
- If checked, a "Save and edit layout" button will be added to the node form on Mercury Editor enabled content types. This button will redirect to Mercury Editor after editing a node.
- Enter the label to display for the  "Save and edit layout" button.
