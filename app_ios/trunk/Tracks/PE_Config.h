//
//  PE_Config.h
//  PonyEngineFoundation
//
//  Created by Savalas Colbert on 12/28/13.
//  Copyright (c) 2013 PonyEngine. All rights reserved.
//

#import <Foundation/Foundation.h>

@interface PE_Config : NSObject

//Define Constants, Settings, States
//Stage_Offline
#define PE_Host_Stage_Offline                @"http://api.familyheartbeat.com/api/"
#define PE_JSON_LogggedOn                    @"{data ={role = developer;tsLastLogin = 1388269038; userAuthToken = '{F8E78715-ABF5-0388-0B1C-845C7ED77BE4}';userId = 2;userIsAuthenticated = 1;version = '0.0.0';};status={code = 200;msg ='User Is Authenticated';};}"

//Stage_Online
#define PE_Host_Stage_Online                 @"http://api.familyheartbeat.com/api/"

//Production
#define PE_TestFlight_Production             @"7fda927c-ad79-4389-aec1-487fb1df30d5"
#define PE_SCSessionStateChangedNotification @"com.PonyEngine.Tracks:SCSessionStateChangedNotification"
#define PE_Host_Production                   @"http://tracksapi.ponyengine.com/api/"
#define PE_Policy_Terms_Production           @"http://tracks.ponyengine.com/policy/terms"
#define PE_Policy_Privacy_Production         @"http://tracks.ponyengine.com/policy/privacy"


typedef enum {
   STATE_NOTSTARTED_LOCK,
   STATE_LIVE_EDITABLE,
   STATE_LIVE_LOCKED,
   STATE_ENDED_LOCKED,
   STATE_ENDED_EDITABLE
} ViewState;


typedef enum {
   REFRESH_FIRST,
   REFRESH_LASTCELL,
   REFRESH_TIMER,
   REFRESH_NEWBET,
   REFRESH_REFRESH
} RefreshType;


typedef enum {
   TAB_HOME,
   TAB_1,
   TAB_2,
   TAB_3
} TabType;

@end
