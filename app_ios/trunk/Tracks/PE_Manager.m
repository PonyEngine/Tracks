//
//  PE_Manager.m
//  BetOnit
//
//  Created by Savalas Colbert on 5/11/13.
//  Copyright (c) 2013 PonyEngine. All rights reserved.
//

#import "PE_Manager.h"
#import "PE_UtilityMethods.h"
#import "AFHTTPClient.h"
#import "AFJSONRequestOperation.h"
#import "KeychainItemWrapper.h"
@interface PE_Manager (private)

@end

static PE_Manager *sharedMyManager = nil;

@implementation PE_Manager
@synthesize isDevMode=_isDevMode;
@synthesize hostChanged=_hostChanged;
@synthesize isHostReach=_isHostReach;
@synthesize hostURL=_hostURL;
@synthesize isInternetReach=_isInternetReach;
@synthesize isWifiReach=_isWifiReach;
@synthesize isActive=_isActive;
@synthesize isUnderIdleControl=_isUnderIdleControl;
@synthesize isOnline=_isOnline;
@synthesize sessionCurrentInfo =_sessionCurrentInfo;
@synthesize networkAvailable=_networkAvailable;
@synthesize isLocationReady=_isLocationReady;
@synthesize delegate=_delegate;
@synthesize userSession=_userSession;
@synthesize hostServer=_hostServer;
@synthesize mainViewController=_mainViewController;
//@synthesize facebook=_facebook;
#pragma mark Singleton Methods
+ (PE_Manager *)sharedManager {
    @synchronized(self) {
        if(sharedMyManager == nil)
            sharedMyManager = [[super allocWithZone:NULL] init];
    }
    return sharedMyManager;
}


+ (id)allocWithZone:(NSZone *)zone {
    return [self sharedManager];
}

- (id)init{
    if (self = [super init]) {
        self.isDevMode=FALSE;
        self.hostURL=nil;
        self.hostChanged=NO;
        self.isOnline=YES;
        self.isUnderIdleControl=YES;
        self.filmLikes=[[NSMutableArray alloc] initWithCapacity:5];
        [UIApplication sharedApplication].networkActivityIndicatorVisible=NO;
        self.filmsLiked=false;
        self.sessionCurrentInfo=[[PE_CurrentInfo alloc] init];
        [self signInStartup:self];
        self.locationManager= [[CLLocationManager alloc] init];
        self.mainViewController=[[UIViewController alloc] init];
        _locationManager.delegate = self;
        _locationManager.desiredAccuracy = kCLLocationAccuracyKilometer;
        // Set a movement threshold for new events.
        _locationManager.distanceFilter = 500;
        [_locationManager startMonitoringSignificantLocationChanges];
        
    }
    [self turnOffDevMode];
    return self;
}

- (id)copyWithZone:(NSZone *)zone {
    return self;
}

/*- (id)retain {
    return self;
}

- (unsigned)retainCount {
    return UINT_MAX; //denotes an object that cannot be released
}

- (oneway void)release {
    // never release
}

- (id)autorelease {
    return self;
}*/


-(BOOL)reachable {
	//	Reachability *r = [Reachability reachabilityWithHostName:@"service.spontt.com"];
	//	NetworkStatus internetStatus = [r currentReachabilityStatus];
    /*if(internetStatus == NotReachable) {
     return NO;
     }*/
    return YES;
}

- (void) configureReachability: (Reachability*) curReach
{
    NetworkStatus netStatus = [curReach currentReachabilityStatus];
    BOOL connectionRequired= [curReach connectionRequired];
    NSString* statusString= @"";
    
    switch (netStatus)
    {
        case NotReachable:
        {
            ///statusString = @"Access Not Available";
            //imageView.image = [UIImage imageNamed: @"stop-32.png"] ;
            //Minor interface detail- connectionRequired may return yes, even when the host is unreachable.  We cover that up here...
            connectionRequired= NO;
            self.isHostReach=NO;
            break;
        }
            
        case ReachableViaWWAN:
        {
            //statusString = @"Reachable WWAN";
            //imageView.image = [UIImage imageNamed: @"WWAN5.png"];
            self.isInternetReach=YES;
            break;
        }
        case ReachableViaWiFi:
        {
            //statusString= @"Reachable WiFi";
            //imageView.image = [UIImage imageNamed: @"Airport.png"];
            self.isWifiReach=YES;
            break;
        }
    }
    if(connectionRequired)
    {
        statusString= [NSString stringWithFormat: @"%@, Connection Required", statusString];
    }
    
}


//&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&
//&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&

#pragma  KeyChain
/*- (NSString *)retrievePasswordFromKeyChain:(NSString *)lastLoginUsername {
 NSError *error;
 NSString *serviceName=@"u2";
 NSString *password=[SFHFKeychainUtils getPasswordForUsername:lastLoginUsername andServiceName:serviceName error:&error];
 return password;
 }*/

-(void)signInStartup:(id)sender{
    //Deallocate Current Info
    //if(!self.sessionCurrentInfo)self.sessionCurrentInfo=[[PE_CurrentInfo alloc] init];
    
}

-(void)signOutCleanup:(id)sender{
    //Deallocate Current Info
    if(self.sessionCurrentInfo){
        [self.sessionCurrentInfo  eraseInfoForUser];
    }
    
    //Clean-Up KeyChain Access
    [self.userSession resetKeychainItem];
    
}

-(void) turnOnDevMode{
   self.isDevMode=YES;
   self.hostURL=[NSURL URLWithString:PE_Host_Stage_Online];
  //self.hostURL=[NSURL URLWithString:@"http://svapi.ponyengine.com/api/"];
   self.hostServer=0;
   self.hostChanged=YES;
}

-(void) turnOffDevMode{
   self.isDevMode=NO;
   self.hostURL=[NSURL URLWithString:PE_Host_Production];
   self.hostServer=1;
   self.hostChanged=YES;
}





/*- (void)dealloc {
    //Networks
    [_queue release];
    [_importQueue release];
    [mtManagedObjectContext_ release];
    [persistentStoreCoordinator_ release];
    [super dealloc];
}*/

@end
