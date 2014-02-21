//
//  PEAppDelegate.m
//  PonyEngineFoundation
//
//  Created by Savalas Colbert on 12/12/13.
//  Copyright (c) 2013 PonyEngine. All rights reserved.
//

#import "PEAppDelegate.h"
#import <Security/Security.h>
#import <QuartzCore/QuartzCore.h>
#import "KeychainItemWrapper.h"
#import "PE_LoginViewController.h"
#import "AFHTTPClient.h"
#import "AFAppPHPClient.h"
#import "AFJSONRequestOperation.h"
#import "PE_Manager.h"


//To Remove
//#import "HomeViewController.h"
NSString *const SCSessionStateChangedNotification = PE_SCSessionStateChangedNotification;

@interface PEAppDelegate ()
@property (strong, nonatomic) UINavigationController *navController;

- (void)showLoginView;
@end

@implementation PEAppDelegate


@synthesize managedObjectContext = _managedObjectContext;
@synthesize managedObjectModel = _managedObjectModel;
@synthesize persistentStoreCoordinator = _persistentStoreCoordinator;
@synthesize navController=_navController;
@synthesize theLoginVC=_theLoginVC;


- (BOOL)application:(UIApplication *)application didFinishLaunchingWithOptions:(NSDictionary *)launchOptions
{
   [TestFlight takeOff:PE_TestFlight_Production];
   
   // Let the device know we want to receive push notifications
	[[UIApplication sharedApplication] registerForRemoteNotificationTypes:
    (UIRemoteNotificationTypeBadge | UIRemoteNotificationTypeSound | UIRemoteNotificationTypeAlert)];
   

   
   
#define TESTING 1  //Remove before submitting to Apple
#ifdef TESTING
    // [TestFlight setDeviceIdentifier:[[UIDevice currentDevice] uniqueIdentifier]];
#endif
   //STATS
   // start of your application:didFinishLaunchingWithOptions // ...
   //[TestFlight takeOff:@"fa74bb63-95d3-47fc-8048-198e96bfab13"];
   // The rest of your application:didFinishLaunchingWithOptions method// ...
   
   //**SETTINGS BUNDLE
   // Set the application defaults
   //NSUserDefaults *defaults = [NSUserDefaults standardUserDefaults];
   //NSDictionary *appDefaults = [NSDictionary dictionaryWithObject:@"YES"
   //  forKey:@"savetoalbum"];
   //[defaults registerDefaults:appDefaults];
   //[defaults synchronize];
   
   //Keychain Access
    KeychainItemWrapper *wrapper = [[KeychainItemWrapper alloc] initWithIdentifier:@"userSession" accessGroup:nil];
    self.userSession = wrapper;
    
    
    /*
    // BUG WORKAROUND:
    // Nib files require the type to have been loaded before they can do the
    // wireup successfully.
    // http://stackoverflow.com/questions/1725881/unknown-class-myclass-in-interface-builder-file-error-at-runtime
    [FBProfilePictureView class];
    */
   
   /* self.window = [[[UIWindow alloc] initWithFrame:[[UIScreen mainScreen] bounds]] autorelease];
    
    self.aMenuVC=[[MenuViewController alloc] init];
    BetTabBarViewController *aBetOnItTVC=[[BetTabBarViewController alloc] init];
    
    //[PE_Manager sharedManager].sessionCurrentInfo
    
    [PE_Manager sharedManager].mainViewController=aBetOnItTVC;
    
    self.navController=[[UINavigationController alloc] initWithRootViewController:aBetOnItTVC];
    
    
    self.revealController=[[ZUUIRevealController alloc] initWithFrontViewController:self.navController rearViewController:self.aMenuVC];
    [self.window addSubview:self.revealController.view];
    [self.window makeKeyAndVisible];
    
    [self showLoginView]; //Handles whether to display Login
    */

   [self.window makeKeyAndVisible];   
   return YES;
}

-(void)revealApp:(id)sender{
   if ([ PE_Manager sharedManager].sessionCurrentInfo.userAuthToken) { //Check of session present
      
      //Startup
      //[self.aMenuVC refreshOpenInvites:self];
      
      UIViewController *topVC=[[[UIApplication sharedApplication] keyWindow] rootViewController];
      
      if ([topVC isKindOfClass:[PE_LoginViewController class]]) {
         //tell it to seque
         PE_LoginViewController *loginTopVC=(PE_LoginViewController *)topVC;
         [loginTopVC goToStartUpView:self];
      }
      
      
      //Get More User Profile Info
      
      //Save the Keychain Info here
      /*if([ PE_Manager sharedManager].sessionCurrentInfo.userId){
         [self.userSession setObject:[ PE_Manager sharedManager].sessionCurrentInfo.userId forKey:(id)kSecAttrAccount];
      }
      
      if([ PE_Manager sharedManager].sessionCurrentInfo.userAuthToken){
         [self.userSession setObject:[ PE_Manager sharedManager].sessionCurrentInfo.userAuthToken forKey:(id)kSecValueData];
      }*/
      
   }
}


-(void)concealApp:(id)sender{
   UIViewController *topViewController = [self.navController topViewController];
   [topViewController presentViewController:self.theLoginVC animated:NO completion:^{
      //What to do here
   }];
}



#pragma mark -
#pragma mark Facebook Login Code

- (void)showLoginView {
   UIViewController *topViewController = [self.navController topViewController];
   UIViewController *modalViewController = [topViewController modalViewController];
   
   NSString *userId=nil;//=[self.userSession objectForKey:(id)kSecAttrAccount];
   NSString *userAuthTok=nil;//=[self.userSession objectForKey:(id)kSecValueData];
   
   //First Check the userSession from the keyChain
   if (userId && userAuthTok) { //existing user session
      [ PE_Manager sharedManager].sessionCurrentInfo.userId=userId;
      [ PE_Manager sharedManager].sessionCurrentInfo.userAuthToken=userAuthTok;
   }else{
      // If the login screen is not already displayed, display it. If the login screen is displayed, then
      // getting back here means the login in progress did not successfully complete. In that case,
      // notify the login view so it can update its UI appropriately.
     /* if (![modalViewController isKindOfClass:[BetTabBarViewController class]]) {
         if ( [(NSString*)[UIDevice currentDevice].model isEqualToString:@"iPad"] ) {
            self.theLoginVC = [[PE_LoginViewController alloc]initWithNibName:@"LoginViewController~iPad" bundle:nil];
         }else{
            self.theLoginVC = [[PE_LoginViewController alloc]init];
         }
         
         [topViewController presentViewController:self.theLoginVC animated:NO completion:^{}];
      } else {
         self.theLoginVC= (PE_LoginViewController*)modalViewController;
         // [LoginViewController loginFailed];
      }*/
   }
}

- (void)sessionStateChanged:(FBSession *)session
                      state:(FBSessionState)state
                      error:(NSError *)error
{
   // FBSample logic
   // Any time the session is closed, we want to display the login controller (the user
   // cannot use the application unless they are logged in to Facebook). When the session
   // is opened successfully, hide the login controller and show the main UI.
   switch (state) {
      case FBSessionStateOpen: {
         
         //State Open so now Authenticate with Server
         [PE_LoginViewController authenticateUserByFB:self];
         
         // FBSample logic
         // Pre-fetch and cache the friends for the friend picker as soon as possible to improve
         // responsiveness when the user tags their friends.
         FBCacheDescriptor *cacheDescriptor = [FBFriendPickerViewController cacheDescriptor];
         [cacheDescriptor prefetchAndCacheForSession:session];
      }
         break;
      case FBSessionStateClosed:
      case FBSessionStateClosedLoginFailed:
         // FBSample logic
         // Once the user has logged in, we want them to be looking at the root view.
         [self.navController popToRootViewControllerAnimated:NO];
         
         [FBSession.activeSession closeAndClearTokenInformation];
         
         [self showLoginView];
         break;
      default:
         break;
   }
   
   [[NSNotificationCenter defaultCenter] postNotificationName:SCSessionStateChangedNotification
                                                       object:session];
   
   if (error) {
      UIAlertView *alertView = [[UIAlertView alloc] initWithTitle:@"Error"
                                                          message:error.localizedDescription
                                                         delegate:nil
                                                cancelButtonTitle:@"OK"
                                                otherButtonTitles:nil];
      [alertView show];
   }
}

- (BOOL)openSessionWithAllowLoginUI:(BOOL)allowLoginUI {
   NSArray *permissions = [NSArray arrayWithObjects:@"email",@"publish_actions", @"user_photos",nil];
   return [FBSession openActiveSessionWithPermissions:permissions
                                         allowLoginUI:allowLoginUI
                                    completionHandler:^(FBSession *session, FBSessionState state, NSError *error) {
                                       [self sessionStateChanged:session state:state error:error];
                                    }];
}



- (void)applicationWillResignActive:(UIApplication *)application
{
   // Sent when the application is about to move from active to inactive state. This can occur for certain types of temporary interruptions (such as an incoming phone call or SMS message) or when the user quits the application and it begins the transition to the background state.
   // Use this method to pause ongoing tasks, disable timers, and throttle down OpenGL ES frame rates. Games should use this method to pause the game.
}

- (void)applicationDidEnterBackground:(UIApplication *)application
{
   // Use this method to release shared resources, save user data, invalidate timers, and store enough application state information to restore your application to its current state in case it is terminated later.
   // If your application supports background execution, this method is called instead of applicationWillTerminate: when the user quits.
}

- (void)applicationWillEnterForeground:(UIApplication *)application
{
   // Called as part of the transition from the background to the inactive state; here you can undo many of the changes made on entering the background.
}

- (void)applicationDidBecomeActive:(UIApplication *)application
{
   // this means the user switched back to this app without completing a login in Safari/Facebook App
   if (FBSession.activeSession.state == FBSessionStateCreatedOpening) {
      // BUG: for the iOS 6 preview we comment this line out to compensate for a race-condition in our
      // state transition handling for integrated Facebook Login; production code should close a
      // session in the opening state on transition back to the application; this line will again be
      // active in the next production rev
      [FBSession.activeSession close]; // so we close our session and start over
   }
}

- (void)applicationWillTerminate:(UIApplication *)application
{
   // Called when the application is about to terminate. Save data if appropriate. See also applicationDidEnterBackground:.
   [FBSession.activeSession close];
}

- (void)saveContext
{
   NSError *error = nil;
   NSManagedObjectContext *managedObjectContext = self.managedObjectContext;
   if (managedObjectContext != nil) {
      if ([managedObjectContext hasChanges] && ![managedObjectContext save:&error]) {
         // Replace this implementation with code to handle the error appropriately.
         // abort() causes the application to generate a crash log and terminate. You should not use this function in a shipping application, although it may be useful during development.
         NSLog(@"Unresolved error %@, %@", error, [error userInfo]);
         abort();
      }
   }
}

#pragma mark - Core Data stack

// Returns the managed object context for the application.
// If the context doesn't already exist, it is created and bound to the persistent store coordinator for the application.
- (NSManagedObjectContext *)managedObjectContext
{
   if (_managedObjectContext != nil) {
      return _managedObjectContext;
   }
   
   NSPersistentStoreCoordinator *coordinator = [self persistentStoreCoordinator];
   if (coordinator != nil) {
      _managedObjectContext = [[NSManagedObjectContext alloc] init];
      [_managedObjectContext setPersistentStoreCoordinator:coordinator];
   }
   return _managedObjectContext;
}

// Returns the managed object model for the application.
// If the model doesn't already exist, it is created from the application's model.
- (NSManagedObjectModel *)managedObjectModel
{
   if (_managedObjectModel != nil) {
      return _managedObjectModel;
   }
   NSURL *modelURL = [[NSBundle mainBundle] URLForResource:@"VMote" withExtension:@"momd"];
   _managedObjectModel = [[NSManagedObjectModel alloc] initWithContentsOfURL:modelURL];
   return _managedObjectModel;
}

// Returns the persistent store coordinator for the application.
// If the coordinator doesn't already exist, it is created and the application's store added to it.
- (NSPersistentStoreCoordinator *)persistentStoreCoordinator
{
   if (_persistentStoreCoordinator != nil) {
      return _persistentStoreCoordinator;
   }
   
   NSURL *storeURL = [[self applicationDocumentsDirectory] URLByAppendingPathComponent:@"VMote.sqlite"];
   
   NSError *error = nil;
   _persistentStoreCoordinator = [[NSPersistentStoreCoordinator alloc] initWithManagedObjectModel:[self managedObjectModel]];
   if (![_persistentStoreCoordinator addPersistentStoreWithType:NSSQLiteStoreType configuration:nil URL:storeURL options:nil error:&error]) {
      /*
       Replace this implementation with code to handle the error appropriately.
       
       abort() causes the application to generate a crash log and terminate. You should not use this function in a shipping application, although it may be useful during development.
       
       Typical reasons for an error here include:
       * The persistent store is not accessible;
       * The schema for the persistent store is incompatible with current managed object model.
       Check the error message to determine what the actual problem was.
       
       
       If the persistent store is not accessible, there is typically something wrong with the file path. Often, a file URL is pointing into the application's resources directory instead of a writeable directory.
       
       If you encounter schema incompatibility errors during development, you can reduce their frequency by:
       * Simply deleting the existing store:
       [[NSFileManager defaultManager] removeItemAtURL:storeURL error:nil]
       
       * Performing automatic lightweight migration by passing the following dictionary as the options parameter:
       @{NSMigratePersistentStoresAutomaticallyOption:@YES, NSInferMappingModelAutomaticallyOption:@YES}
       
       Lightweight migration will only work for a limited set of schema changes; consult "Core Data Model Versioning and Data Migration Programming Guide" for details.
       
       */
      NSLog(@"Unresolved error %@, %@", error, [error userInfo]);
      abort();
   }
   
   return _persistentStoreCoordinator;
}

- (BOOL)application:(UIApplication *)application
            openURL:(NSURL *)url
  sourceApplication:(NSString *)sourceApplication
         annotation:(id)annotation {
   // attempt to extract a token from the url
   return [FBSession.activeSession handleOpenURL:url];
}



#pragma mark - Application's Documents directory

// Returns the URL to the application's Documents directory.
- (NSURL *)applicationDocumentsDirectory
{
   return [[[NSFileManager defaultManager] URLsForDirectory:NSDocumentDirectory inDomains:NSUserDomainMask] lastObject];
}

#pragma mark- NSNotifications
- (void)application:(UIApplication*)application didRegisterForRemoteNotificationsWithDeviceToken:(NSData*)deviceToken
{
	NSLog(@"My token is: %@", deviceToken);
   NSString *newToken = [deviceToken description];
	newToken = [newToken stringByTrimmingCharactersInSet:[NSCharacterSet characterSetWithCharactersInString:@"<>"]];
	[PE_Manager sharedManager].sessionCurrentInfo.deviceToken = [newToken stringByReplacingOccurrencesOfString:@" " withString:@""];
}

- (void)application:(UIApplication*)application didFailToRegisterForRemoteNotificationsWithError:(NSError*)error
{
	NSLog(@"Failed to get token, error: %@", error);
}

- (void)application:(UIApplication*)application didReceiveRemoteNotification:(NSDictionary*)userInfo
{

}

-(void)storeSessionData
{
   if (!self.userSession)
   {
      return;
   }
   
   //Save the Keychain Info here
   if([PE_Manager sharedManager].sessionCurrentInfo.userId)
   {
      NSLog(@"setting user id here %@",[PE_Manager sharedManager].sessionCurrentInfo.userId);
      [self.userSession setObject:[PE_Manager sharedManager].sessionCurrentInfo.userId forKey:(__bridge id)kSecAttrAccount];
   }
   
   if([PE_Manager sharedManager].sessionCurrentInfo.userAuthToken)
   {
      NSLog(@"setting token here %@",[PE_Manager sharedManager].sessionCurrentInfo.userAuthToken);
      [self.userSession setObject:[PE_Manager sharedManager].sessionCurrentInfo.userAuthToken forKey:(__bridge id)kSecValueData];
   }
}

-(void)clearSessionData
{
   [PE_Manager sharedManager].sessionCurrentInfo.userId=nil;
   [PE_Manager sharedManager].sessionCurrentInfo.userAuthToken=nil;
   [self.userSession resetKeychainItem];
}

@end
