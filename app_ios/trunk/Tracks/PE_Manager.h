//
//  PE_Manager.h
//  BetOnit
//
//  Created by Savalas Colbert on 5/11/13.
//  Copyright (c) 2013 PonyEngine. All rights reserved.
//

#import <Foundation/Foundation.h>
#import <CoreLocation/CoreLocation.h>
#import <CoreData/CoreData.h>
#import <MapKit/MapKit.h>
#import <MessageUI/MessageUI.h>
#import "Reachability.h"
#import "PE_CurrentInfo.h"
#import "PEAppDelegate.h"

@class PE_User;
@class ASINetworkQueue;
@class HJObjManager;
@class ShoppingCart;
@class Store;
@class MenuItem;

@class KeychainItemWrapper;

@protocol PE_ManagerDelegate
@optional
-(void) findingLocation;
-(void) foundLocation;
-(void) findingStores;
-(void) findingStoresFinished;
-(void)locationUpdate:(CLLocation *)location; // Our location updates are sent here
-(void)locationError:(NSError *)error; // Any errors are sent here
@end

@interface PE_Manager : NSObject <CLLocationManagerDelegate,NSFetchedResultsControllerDelegate> {
    //NSMutableArray *archivedVokels;
    PE_CurrentInfo *_sessionCurrentInfo;
    NSMutableString *_curRadius; //possibly store in core data
	NSDate *_curRadiusTime;
    
    //LocationManager
	CLLocationManager *_locationManager;
	CLLocation *_curLocation;
    
	//id <PE_ManagerDelegate> _delegate;
    BOOL _isDevMode;
    BOOL _isUnderIdleControl;
    BOOL _isOnline;
	BOOL _isProcessingImport;
	BOOL _isLocationReady;
	BOOL _networkAvailable;
	
	BOOL _syncTags;
	
	BOOL _isHostReach;
	BOOL _isInternetReach;
	BOOL _isWifiReach;
	BOOL _isActive;
	
	NSDate *_lastRequestTime;
    

    
	//Networks
	ASINetworkQueue *_queue;
	ASINetworkQueue *_importQueue;
	
    HJObjManager *_imgMan;
    KeychainItemWrapper *userSession;
    UIViewController *_mainViewController;
    
    
@private
    NSManagedObjectContext *mtManagedObjectContext_;
    NSPersistentStoreCoordinator *persistentStoreCoordinator_;
}
@property (nonatomic,retain) NSURL *hostURL;
@property (nonatomic,assign) int hostServer; //0=Production 1=stage
@property (nonatomic,assign) BOOL *hostChanged;
@property (nonatomic,retain) UIView *loggedInView;
@property (nonatomic,retain) PE_CurrentInfo *sessionCurrentInfo;

//User Info
@property (nonatomic,retain) NSMutableString *curRadius;
@property (nonatomic,retain) NSDate *curRadiusTime;


@property (assign) id <PE_ManagerDelegate> delegate;
@property (assign) BOOL isDevMode;
@property (nonatomic,assign) BOOL isUnderIdleControl;
@property (nonatomic,assign) BOOL isOnline;
@property (nonatomic,assign) BOOL networkAvailable;
@property (nonatomic,assign) BOOL isLocationReady;

//Navigationbar Views
@property (nonatomic,assign) BOOL isHostReach;
@property (nonatomic,assign) BOOL isInternetReach;
@property (nonatomic,assign) BOOL isWifiReach;
@property (nonatomic,assign) BOOL isActive;
@property (nonatomic,retain) NSDate  *lastRequestTime;


//Location Management
@property (nonatomic,retain) CLLocationManager *locationManager;
@property (nonatomic,retain) CLLocation *curLocation;


//Network
@property (nonatomic,retain) ASINetworkQueue *queue;

//Caching of the images
@property (nonatomic,retain) HJObjManager *imgMan;

@property (nonatomic,retain) NSMutableArray *filmLikes;
@property (nonatomic,assign) BOOL *filmsLiked;

@property (nonatomic,retain) NSManagedObjectContext *mtManagedObjectContext;
@property (nonatomic, retain) NSPersistentStoreCoordinator *persistentStoreCoordinator;

@property (nonatomic, retain) KeychainItemWrapper *userSession;;

@property (nonatomic,retain)UIViewController *mainViewController;

//Class Method
+ (PE_Manager *)sharedManager;
- (void)populateUserDetails;
- (BOOL)reachable;
- (NSPersistentStoreCoordinator *)persistentStoreCoordinator;

-(void) authenticateUserByFB:(id)sender;

-(void) turnOnDevMode;
-(void) turnOffDevMode;

//&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&
//&& Location Manager
//&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&
//- (CLLocationManager *)initLocationManager;
//- (float)getCurRadiusDegree;
//- (BOOL)isTimeForRequest;

-(void) configureReachability:(Reachability*) curReach;
-(void)fbPopulateUserDetails;
-(void)signOutCleanup:(id)sender;
//- (NSString *)retrievePasswordFromKeyChain:(NSString *)lastLoginUsername;

//-(void)refreshData:(id)sender;

@end
