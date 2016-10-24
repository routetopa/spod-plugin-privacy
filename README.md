# SPOD Privacy

This plugin allow platform administrators to hide certain pages from guest users. Control is at Controller and Actions
level.

## Installation

Clone this repository and put its content in `ox_plugins/spodprivacy` directory, then install via administration panel.
 
## Configuration

In the Settings page of the plugin, administrators can set which action from which controller should be hidden to
guests. Each line of the filter must begin with the controller name followed by a colon (`:`) and by the names of
actions to hide. Actions must be separated by a comma (`,`). An asterisk (`*`) means that every action of the controller
must be hid.

You can temporarily enable Debug mode for this plugin: it will show controller and action name for each page you visit.
**Important!** Remember to disable the plugin's debug mode, otherwise users will see the debug message on every page.
 
### Example

- `controller:action`: hide action from controller
- `controller:action1,action2`: hide action1 and action2 from controller
- `controller:*`: hide all actions from controller

## License

The software is released under MIT License.