# froget

A service for helping you remember things.

## Actions

### REMIND

Remind me to do something at a certain time.

If this is done privately, the reminder will be sent to you via DM or push notification.

If this is done in a public place, the reminder is emmited to the public channel.

#### Input

Reminder: The reminder to emit.
Context: AT (absolute) or IN (relative).

Absolute:

-   Date (optional, defaults to today)
-   Time (HH:MM:SS) Only Hours are required.

Relative:

-   Time Amount (numeric): The amount of time to emit the reminder in.
-   Time Unit [Seconds, Hours, Minutes, Hours, Days]: The unit of time to emit the reminder in.

### TODO

Add a todo item to a list.

This is a private action that only you can see.

## Services Roadmap

-   [ ] API
-   [ ] Web
-   [ ] Discord
-   [ ] Slack
-   [ ] Telegram
-   [ ] Push Notifications
