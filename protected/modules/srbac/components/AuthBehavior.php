<?php
/**
 * AuthBehavior class file.
 * @author Christoffer Niska <ChristofferNiska@gmail.com>
 * @copyright Copyright &copy; Christoffer Niska 2012-
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @package auth.components
 */

/**
 * Auth module behavior for the authorization manager.
 *
 * @property CAuthManager|IAuthManager $owner The authorization manager.
 */
class AuthBehavior extends CBehavior
{
	/**
	 * @var string[] a list of names for the users that should be treated as administrators.
	 */
	public $admins = array('demo');
	
	/**
	 * Returns whether the given item has a specific parent.
	 * @param string $itemName name of the item.
	 * @param string $parentName name of the parent.
	 * @return boolean the result.
	 */
	public function hasParent($itemName, $parentName)
	{
		$permissions = $this->getItemPermissions($parentName);
		return isset($permissions[$itemName]);
	}

	/**
	 * Returns whether the given item has a specific child.
	 * @param string $itemName name of the item.
	 * @param string $childName name of the child.
	 * @return boolean the result.
	 */
	public function hasChild($itemName, $childName)
	{
		$permissions = $this->getItemPermissions($itemName);
		return isset($permissions[$childName]);
	}

	/**
	 * Returns whether the given item has a specific ancestor.
	 * @param string $itemName name of the item.
	 * @param string $descendantName name of the ancestor.
	 * @return boolean the result.
	 */
	public function hasAncestor($itemName, $ancestorName)
	{
		$ancestors = $this->getAncestors($itemName);
		return isset($ancestors[$ancestorName]);
	}

	/**
	 * Returns whether the given item has a specific descendant.
	 * @param string $itemId id of the item.
	 * @param string $descendantId id of the descendant.
	 * @return boolean the result.
	 */
	public function hasDescendant($itemId, $descendantId)
	{
		$descendants = $this->getDescendants($itemId);
		return isset($descendants[$descendantId]);
	}

	/**
	 * Returns all ancestors for the given item recursively.
	 * @param string $itemId id of the item.
	 * @param array|null $permissions permissions to process.
	 * @return array the ancestors.
	 */
	public function getAncestors($itemId, $permissions = null)
	{
		$ancestors = array();

		if ($permissions === null)
			$permissions = $this->getPermissions();

		foreach ($permissions as $childId => $child)
		{
			if ($this->hasDescendant($childId, $itemId)) {
				$ancestors[$childId] = $child;
            }

            if(count($child['children']) > 0)
                $ancestors = TUtil::mergeArrayByOverRide($ancestors, $this->getAncestors($itemId, $child['children']));
		}

		return $ancestors;
	}

	/**
	 * Returns all the descendants for the given item recursively.
	 * @param string $itemId name of the item.
	 * @return array the descendants.
	 */
	public function getDescendants($itemId)
	{
		$itemPermissions = $this->getItemPermissions($itemId);
		return $this->flattenPermissions($itemPermissions);
	}

	/**
	 * Returns the permission tree for the given items.
	 * @param CAuthItem[] $items items to process. If omitted the complete tree will be returned.
	 * @param integer $depth current depth.
	 * @return array the permissions.
	 */
	private function getPermissions($items = null, $depth = 0)
	{
		$permissions = array();

		if ($items === null)
			$items = $this->owner->getAuthItems();

		foreach ($items as $itemId => $item)
		{
			$permissions[$itemId] = array(
				'id' => $itemId,
				'item' => $item,
				//'children' => $this->getPermissions($item->getChildren(), $depth + 1),
                'children' => array(),
				'depth' => $depth,
			);
		}

		return $permissions;
	}

	/**
	 * Builds the permissions for the given item.
	 * @param string $itemId name of the item.
	 * @return array the permissions.
	 */
	private function getItemPermissions($itemId)
	{
		$item = $this->owner->getAuthItem($itemId);
		return $item instanceof CAuthItem ? $this->getPermissions($item->getChildren()) : array();
	}

	/**
	 * Returns the permissions for the items with the given ids.
	 * @param string[] $ids list of item ids.
	 * @return array the permissions.
	 */
	public function getItemsPermissions($ids)
	{
		$permissions = array();

		$items = $this->getPermissions();
		$flat = $this->flattenPermissions($items);

		foreach ($flat as $itemId => $item)
		{
			if (in_array($item['id'], $ids))
				$permissions[$itemId] = $item;
		}

		return $permissions;
	}

	/**
	 * Flattens the given permission tree.
	 * @param array $permissions the permissions tree.
	 * @return array the permissions.
	 */
	public function flattenPermissions($permissions)
	{
		$flattened = array();
		foreach ($permissions as $itemId => $itemPermissions)
		{
			$children = $itemPermissions['children'];
			unset($itemPermissions['children']); // not needed in a flat tree
			$flattened[$itemId] = $itemPermissions;
			$flattened = TUtil::mergeArrayByOverRide($flattened, $this->flattenPermissions($children));
		}

		return $flattened;
	}
}
