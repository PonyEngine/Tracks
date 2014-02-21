//
//  PEAppDelegate.h
//  PonyEngineFoundation
//
//  Created by Savalas Colbert on 12/12/13.
//  Copyright (c) 2013 PonyEngine. All rights reserved.
//

#import <UIKit/UIKit.h>
@class  PE_LoginViewController;
@class KeychainItemWrapper;
@interface PEAppDelegate : UIResponder <UIApplicationDelegate>
@property (strong, nonatomic) UIWindow *window;
@property (nonatomic, retain) PE_LoginViewController *theLoginVC;
@property (readonly, strong, nonatomic) NSManagedObjectContext *managedObjectContext;
@property (readonly, strong, nonatomic) NSManagedObjectModel *managedObjectModel;
@property (readonly, strong, nonatomic) NSPersistentStoreCoordinator *persistentStoreCoordinator;
@property (nonatomic, strong) KeychainItemWrapper *userSession;

- (void)saveContext;
- (NSURL *)applicationDocumentsDirectory;
- (BOOL)openSessionWithAllowLoginUI:(BOOL)allowLoginUI;
- (void)revealApp:(id)sender;
- (void)concealApp:(id)sender;
- (void)showLoginView;
- (void)storeSessionData;
- (void)clearSessionData;

@end
