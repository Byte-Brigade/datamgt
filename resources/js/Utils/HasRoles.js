function hasRoles(roles, auth) {
  const roleArr = roles.split("|");

  return roleArr.includes(auth.role);
};

export { hasRoles };
