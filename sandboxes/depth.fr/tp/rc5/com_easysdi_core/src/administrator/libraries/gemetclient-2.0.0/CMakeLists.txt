# FIXME: nedetekovat kompilator, nepotrebujeme
project(gemet C)
SET(GEMET_VERSION "1.0.0")

SET(CMAKE_MODULE_PATH "${CMAKE_SOURCE_DIR}/cmake")
CMAKE_MINIMUM_REQUIRED(VERSION 2.6.0)                                                                                                  
CMAKE_MINIMUM_REQUIRED(VERSION 2.6.0 FATAL_ERROR)                                                                                      
IF(COMMAND cmake_policy)
	cmake_policy(SET CMP0003 NEW)
ENDIF(COMMAND cmake_policy)
SET(CMAKE_COLOR_MAKEFILE ON)

OPTION(WITH_Parent "Enable parent integration. Embedding into another project (catalogue)" FALSE)
OPTION(ENABLE_Uncompressed "Enable uncompressed javascript installation. (Great for debugging purposes)" FALSE)
MARK_AS_ADVANCED(WITH_Parent)
MARK_AS_ADVANCED(CMAKE_BUILD_TYPE)
MARK_AS_ADVANCED(CMAKE_INSTALL_PREFIX)
IF(WITH_Parent)
	SET(GEMET_INSTALL_PREFIX "${PARENT_INSTALL_PREFIX}/gemet/${GEMET_VERSION}/" CACHE PATH "Install path prefix")
ELSE(WITH_Parent)
	SET(GEMET_INSTALL_PREFIX "/var/www/wwwlibs/gemet/${GEMET_VERSION}/" CACHE PATH "Install path prefix")
ENDIF(WITH_Parent)

add_subdirectory(src)

configure_file(${CMAKE_CURRENT_SOURCE_DIR}/tools/buildGemetClient.py.in
	${CMAKE_CURRENT_BINARY_DIR}/tools/buildGemetClient.py )
IF(ENABLE_Uncompressed)
	add_custom_target(gemmet ALL
		COMMAND python ${CMAKE_CURRENT_BINARY_DIR}/tools/buildGemetClient.py -u
	)
ELSE(ENABLE_Uncompressed)
	add_custom_target(gemet ALL
		COMMAND python ${CMAKE_CURRENT_BINARY_DIR}/tools/buildGemetClient.py
	)
ENDIF(ENABLE_Uncompressed)
SET(javascript
	"${CMAKE_CURRENT_BINARY_DIR}/GemetClient.js"
)
INSTALL(FILES ${javascript} DESTINATION ${GEMET_INSTALL_PREFIX} ) 
