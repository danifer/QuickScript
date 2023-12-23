#!/usr/bin/osascript

set res to 3
set monitor to 2
tell application "System Preferences"
	activate
	set the current pane to pane id "com.apple.preference.displays"
	delay 1
	tell application "System Events"
		tell window "Displays" of application process "System Preferences"
			click button "Display Settings…"
			delay 1
			select row monitor of outline 1 of scroll area 1 of sheet 1
			click radio button "Default for Display" of radio group 1 of sheet 1
			delay 1
			click radio button "Scaled" of radio group 1 of sheet 1
			delay 1
			set selected of row res of table 1 of scroll area 2 of sheet 1 to true
			delay 0.5
			if not (value of checkbox "Show all resolutions" of sheet 1 as boolean) then
				click checkbox "Show all resolutions" of sheet 1
			end if
			delay 0.5
			set selected of row 2 of table 1 of scroll area 2 of sheet 1 to true
			delay 0.5
			click button "Done" of sheet 1
		end tell
	end tell
end tell
delay 1
tell application "System Preferences" to if it is running then close its front window
