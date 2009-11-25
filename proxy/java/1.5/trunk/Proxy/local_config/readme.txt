create a folder for your personal configs in local_config directory. Ex. toto
Copy web directory content in to your config directory. ex. local_config/toto
Remove recursively .svn directories in each directory in local_config/toto
In eclipse, when you use Launcher.java to launch Proxy, set the first args  value to your personal directory relative path. 
(Launch/Run configurations/Arguments/Program arguments) ex: local_config/toto
Don't forget to mark your folder as svn:ignore.
