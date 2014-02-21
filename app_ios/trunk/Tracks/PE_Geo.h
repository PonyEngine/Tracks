//
//  PE_Geo.h
//  VMote
//
//  Created by Savalas Colbert on 12/19/12.
//  Copyright (c) 2012 Pony Engine. All rights reserved.
//

#import <Foundation/Foundation.h>

@interface PE_Geo : NSObject
    @property (nonatomic,retain) NSNumber *lat;
    @property (nonatomic,retain) NSNumber *lon;
    @property (nonatomic,retain) NSNumber *alt;
    - (id)initWithJSON:(id)userJSON;
@end
