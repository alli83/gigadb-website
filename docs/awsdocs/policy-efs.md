```
{
	"Version": "2012-10-17",
	"Statement": [
		{
			"Sid": "EFSFullPermissions",
			"Effect": "Allow",
			"Action": [
				"elasticfilesystem:DescribeBackupPolicy",
				"elasticfilesystem:DeleteAccessPoint",
				"elasticfilesystem:DescribeReplicationConfigurations",
				"elasticfilesystem:UntagResource",
				"elasticfilesystem:ListTagsForResource",
				"elasticfilesystem:DeleteReplicationConfiguration",
				"elasticfilesystem:ClientWrite",
				"elasticfilesystem:CreateReplicationConfiguration",
				"elasticfilesystem:DeleteTags",
				"elasticfilesystem:DescribeLifecycleConfiguration",
				"elasticfilesystem:ClientMount",
				"elasticfilesystem:DescribeFileSystemPolicy",
				"elasticfilesystem:PutLifecycleConfiguration",
				"elasticfilesystem:DescribeFileSystems",
				"elasticfilesystem:DeleteMountTarget",
				"elasticfilesystem:CreateAccessPoint",
				"elasticfilesystem:PutFileSystemPolicy",
				"elasticfilesystem:DeleteFileSystemPolicy",
				"elasticfilesystem:ModifyMountTargetSecurityGroups",
				"elasticfilesystem:{
	"Version": "2012-10-17",
	"Statement": [
		{
			"Sid": "EFSFullPermissions",
			"Effect": "Allow",
			"Action": [
				"elasticfilesystem:DescribeBackupPolicy",
				"elasticfilesystem:DeleteAccessPoint",
				"elasticfilesystem:DescribeReplicationConfigurations",
				"elasticfilesystem:UntagResource",
				"elasticfilesystem:ListTagsForResource",
				"elasticfilesystem:DeleteReplicationConfiguration",
				"elasticfilesystem:ClientWrite",
				"elasticfilesystem:CreateReplicationConfiguration",
				"elasticfilesystem:DeleteTags",
				"elasticfilesystem:DescribeLifecycleConfiguration",
				"elasticfilesystem:ClientMount",
				"elasticfilesystem:DescribeFileSystemPolicy",
				"elasticfilesystem:PutLifecycleConfiguration",
				"elasticfilesystem:DescribeFileSystems",
				"elasticfilesystem:DeleteMountTarget",
				"elasticfilesystem:CreateAccessPoint",
				"elasticfilesystem:PutFileSystemPolicy",
				"elasticfilesystem:DeleteFileSystemPolicy",
				"elasticfilesystem:ModifyMountTargetSecurityGroups",
				"elasticfilesystem:DescribeMountTargets",
				"elasticfilesystem:Restore",
				"elasticfilesystem:DescribeAccessPoints",
				"elasticfilesystem:TagResource",
				"elasticfilesystem:CreateTags",
				"elasticfilesystem:UpdateFileSystemProtection",
				"elasticfilesystem:DescribeTags",
				"elasticfilesystem:CreateMountTarget",
				"elasticfilesystem:Backup",
				"elasticfilesystem:PutBackupPolicy",
				"elasticfilesystem:ClientRootAccess",
				"elasticfilesystem:DeleteFileSystem",
				"elasticfilesystem:DescribeMountTargetSecurityGroups",
				"elasticfilesystem:UpdateFileSystem"
			],
			"Resource": "*"
		},
		{
			"Sid": "EFSCreatePermission",
			"Effect": "Allow",
			"Action": [
				"elasticfilesystem:PutAccountPreferences",
				"elasticfilesystem:CreateFileSystem",
				"elasticfilesystem:DescribeAccountPreferences"
			],
			"Resource": "*"
		},
		{
			"Sid": "EFSRequiredForMountTarget",
			"Effect": "Allow",
			"Action": [
				"ec2:DescribeNetworkInterfaceAttribute",
				"kms:ListAliases"
			],
			"Resource": "*"
		}
	]
}Targets",
				"elasticfilesystem:Restore",
				"elasticfilesystem:DescribeAccessPoints",
				"elasticfilesystem:TagResource",
				"elasticfilesystem:CreateTags",
				"elasticfilesystem:UpdateFileSystemProtection",
				"elasticfilesystem:DescribeTags",
				"elasticfilesystem:CreateMountTarget",
				"elasticfilesystem:Backup",
				"elasticfilesystem:PutBackupPolicy",
				"elasticfilesystem:ClientRootAccess",
				"elasticfilesystem:DeleteFileSystem",
				"elasticfilesystem:DescribeMountTargetSecurityGroups",
				"elasticfilesystem:UpdateFileSystem"
			],
			"Resource": "*",
			"Condition": {
				"StringEquals": {
					"aws:RequestedRegion": [
						"ap-east-1",
						"ap-northeast-1",
						"ap-northeast-2",
						"eu-west-3"
					]
				}
			}
		},
		{
			"Sid": "EFSCreatePermission",
			"Effect": "Allow",
			"Action": [
				"elasticfilesystem:PutAccountPreferences",
				"elasticfilesystem:CreateFileSystem",
				"elasticfilesystem:DescribeAccountPreferences"
			],
			"Resource": "*",
			"Condition": {
				"StringEquals": {
					"aws:RequestedRegion": [
						"ap-east-1",
						"ap-northeast-1",
						"ap-northeast-2",
						"eu-west-3"
					]
				}
			}
		},
		{
			"Sid": "EFSRequiredForMountTarget",
			"Effect": "Allow",
			"Action": [
				"ec2:DescribeNetworkInterfaceAttribute"
			],
			"Resource": "*",
			"Condition": {
				"StringEquals": {
					"aws:RequestedRegion": [
						"ap-east-1",
						"ap-northeast-1",
						"ap-northeast-2",
						"eu-west-3"
					]
				}
			}
		}
	]
}
```