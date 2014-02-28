## Logging ##

Move to its own composer project. Promote package to:
`goliatone\logging`
Meaning, remove it from FlatG!

CONVENTIONS:
Still debating over removing underscores to protected/private and using lowercase TRUE/FALSE/NULL.
Should reach a decision before release.

#### TODO ####

- Publishers need to be able to unregister themselves from Manager, so that we might register them but still remove them
from pull...
- TypedSet: Data structure to hold many instances of one Type, and proxy method calls to all it's instances. For CompoundPublisher, CompoundFormatter
- There should be an IFilterable, with addFilter, getFilter, hasFilter, removeFilter, and isFiltered.
- Integrate with a ErrorLogger that registers as an error_handler and exception_handler
- Logger, add assertLog, log if false.


### DEBUG PUBLISHER ###
Environment aware:
- Automatic switch between CLI and Browser publisher.
- Deactivates on DEBUG == FALSE

### ErrorLogger ###
Hook into error handling mechanism. Log errors/exceptions.


#### CONFIGURATION ####
Configuration should have different levels:
- Default configuration
- File configuration, global
- File configuration, instance
- Using Logger API.


### ROADMAP ###
~~Implement filters. Filters at all different levels, and pacakge.~~
Publishers need to be able to unregister at runtime. If conditions are not met, then no need for them to stick around.
Enable remote sessions, where we can configure from profile. We should hook up
a config mechanism.
Make configuration
Make LoggerFactory and LoggerManager


