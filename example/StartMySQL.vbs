Set UAC = CreateObject("Shell.Application")
UAC.ShellExecute "cmd.exe", "/c ""NET START MySQL""", "", "runas", 1
