//
//  PE_CurrentInfo.h
//  VMote
//
//  Created by Savalas Colbert on 12/9/12.
//  Copyright (c) 2012 Pony Engine. All rights reserved.
//

#import <Foundation/Foundation.h>
//@class UserPreferences;

@interface PE_CurrentInfo : NSObject
@property (nonatomic,retain) NSString       *userAuthToken;
@property (nonatomic,retain) NSString       *userId;
@property (nonatomic,retain) NSString       *usernameEmail;
@property (nonatomic,retain) NSString       *userPwd;
@property (nonatomic,retain) NSString       *profileName;
@property (nonatomic,retain) NSString       *firstName;
@property (nonatomic,retain) NSString       *middleName;
@property (nonatomic,retain) NSString       *lastName;
@property (nonatomic,retain) NSURL          *picURL;
@property (nonatomic,retain) NSString       *role;
@property (nonatomic,retain) NSNumber       *tsCreated;
@property (nonatomic,retain) NSNumber       *tsLastLogin;
@property (nonatomic,retain) NSString       *location;
@property (nonatomic,retain) NSString       *bio;
@property (nonatomic,retain) NSString       *gender;
@property (nonatomic,retain) NSString       *fbId;

@property (nonatomic,retain) NSNumber       *bets;
@property (nonatomic,retain) NSNumber       *bucks;
@property (nonatomic,retain) NSNumber       *points;
@property (nonatomic,retain) NSNumber       *level;

@property (nonatomic,retain) NSMutableArray *openInvites;

@property (nonatomic,retain) NSNumber       *betTotalNFL;
@property (nonatomic,retain) NSNumber       *betTotalNBA;
@property (nonatomic,retain) NSNumber       *betTotalMLB;
@property (nonatomic,retain) NSNumber       *betTotalNHL;
@property (nonatomic,retain) NSString       *deviceToken;
@property (nonatomic,retain) NSMutableArray *pushMsgs;
@property (nonatomic,retain) NSMutableArray *winLosses;
@property (nonatomic,assign) BOOL       isNewMember;

- (void) processCredentials:(id)jsonData;
- (void) processProfile:(id)profile;
//- (void)processVideoStream:(NSMutableArray *)theVideosRef withVideoStreamIds:(NSMutableArray *)theVideoIdsRef andVideosToAdd:(NSArray *)theMyVideos andRefreshType:(RefreshType)theRefreshType;
- (NSArray *)processUsers:(NSArray *)users;
- (void) eraseInfoForUser;
- (void)refreshUserProfile:(id)sender forViewAppearVC:(UIViewController *)aViewController;
- (NSString *)fullName;

@end
