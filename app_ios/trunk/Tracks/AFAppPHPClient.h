//
//  AFAppPHPClient.h
//  VMote
//
//  Created by Savalas Colbert on 12/15/12.
//  Copyright (c) 2012 Pony Engine. All rights reserved.
//

#import "AFHTTPClient.h"

@interface AFAppPHPClient : AFHTTPClient
    + (AFAppPHPClient *)sharedClient;
@end
