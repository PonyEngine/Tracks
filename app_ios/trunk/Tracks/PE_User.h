//
//  PE_User.h
//  VMote
//
//  Created by Savalas Colbert on 12/22/12.
//  Copyright (c) 2012 Pony Engine. All rights reserved.
//

#import <Foundation/Foundation.h>

@interface PE_User : NSObject
    @property (nonatomic,retain) NSString *userId;
    @property (nonatomic,retain) NSString *usernameEmail;
    @property (nonatomic,retain) NSString *firstName;
    @property (nonatomic,retain) NSString *fullName;
    @property (nonatomic,retain) NSString *profileName;
    @property (nonatomic,retain) NSNumber *tsCreated;
    @property (nonatomic,retain) NSURL    *picURL;
    @property (nonatomic,retain) NSString *location;
    @property (nonatomic,retain) NSString *bio;
    @property (nonatomic,retain) NSString *gender;
    @property (nonatomic,retain) NSString *fbId;
    @property (nonatomic,retain) NSNumber *points;
    @property (nonatomic,retain) NSNumber *bucks;
    @property (nonatomic,retain) NSNumber *level;
    @property (nonatomic,assign) BOOL following;
    @property (nonatomic,retain) NSNumber *ubi_id;
    @property (nonatomic,retain) NSNumber *team_id;
    @property (nonatomic,retain) NSString *custom_scores;
    @property (nonatomic,retain) NSMutableArray *custom_scores_array;
    @property (nonatomic,retain) NSNumber *response_type;

    - (id)initWithJSON:(id)userJSON;
@end
