# Migrate to 1.7.x

- the Framework\Project interface was updated with new methods (configurationUpdate, defineDirectory, getNamespaceDirectory)
- the Framework\Package\Package interface was update with new methods (defineDirectory)
- AutoLoadInfo::getFiles is now AutoLoadInfo::getFilesInfos and returns the found prefix for each file

## parts
  - installTestSuite is now TestSuite
  - CreateBootstrap is now Bootstrap