locals {

  azs = slice(data.aws_availability_zones.available.names, 0, 3)
  public_subnets   = ["10.99.0.0/24", "10.99.1.0/24", "10.99.2.0/24"]
  private_subnets  = ["10.99.3.0/24", "10.99.4.0/24", "10.99.5.0/24"]

}

data "aws_availability_zones" "available" {
  state = "available"
}

data "aws_caller_identity" "current" {}

data "aws_vpc" "selected" {
  id = var.vpc_id
}

data "aws_region" "current" {}

module "efs" {
  source = "terraform-aws-modules/efs/aws"

  # File system
  name           = "gigadb-efs ${var.deployment_target}"
  creation_token = "gigadb-efs-${data.aws_caller_identity.current.arn}-${var.deployment_target}"



  lifecycle_policy = {
    transition_to_ia                    = "AFTER_30_DAYS"
    transition_to_primary_storage_class = "AFTER_1_ACCESS"
  }

  # File system policy (basic access only, better use IAM policies to add higher level access and/ore more granular)
  attach_policy                      = true
  bypass_policy_lockout_safety_check = false
  policy_statements = [
    {
      sid     = "BasicReadonlyAccess"
      actions = ["elasticfilesystem:ClientMount"]
      principals = [
        {
          type        = "AWS"
          identifiers = [data.aws_caller_identity.current.arn]
        }
      ]
    }
  ]

  # Mount targets / security group
  mount_targets              = { for k, v in zipmap(local.azs, local.private_subnets) : k => { subnet_id = v } }

  security_group_description = "gigadb-efs EFS SG for ${data.aws_caller_identity.current.arn} on ${var.deployment_target}"
  security_group_vpc_id      = data.aws_vpc.selected.id
  security_group_rules = {
    vpc = {
      # relying on the defaults provdied for EFS/NFS (2049/TCP + ingress)
      description = "NFS ingress from VPC private subnets"
      cidr_blocks = local.private_subnets
    }
  }

  # Access point(s)
  access_points = {
    dropbox_area = {

      name = "dropbox-area"

      posix_user = {
        gid            = 1000
        uid            = 1000
      }

      root_directory = {
        path = "/share/dropbox"
        creation_info = {
          owner_gid   = 1000
          owner_uid   = 1000
          permissions = "755"
        }
      }

    }

    configuration_area = {

      name = "configuration-area"

      posix_user = {
        gid            = 1000
        uid            = 1000
      }

      root_directory = {
        path = "/share/config"
        creation_info = {
          owner_gid   = 1000
          owner_uid   = 1000
          permissions = "700"
        }
      }

    }

  }

  # Backup policy
  enable_backup_policy = false

  # Replication (to another region) configuration (see https://docs.aws.amazon.com/efs/latest/ug/efs-replication.html)
  create_replication_configuration = false
  # replication_configuration_destination = {
  #   region = data.aws_region.current.name
  # }

  tags = {
    Owner   = var.owner
    Environment = var.deployment_target
  }
}